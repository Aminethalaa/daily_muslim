<?php

namespace App\Livewire;

use App\Models\AzkarCategory;
use App\Services\Gamification;
use App\Services\Streaks;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('components.layouts.app')]
class AzkarSession extends Component
{
    public AzkarCategory $category;

    /** @var Collection<int,\App\Models\AzkarItem> */
    public Collection $items;

    /** remaining repeats per item id */
    public array $counts = [];

    /** item ids fully completed */
    public array $completed = [];

    public bool $finished = false;

    public function mount(string $key): void
    {
        $this->category = AzkarCategory::where('key', $key)->with('items')->firstOrFail();
        $this->items = $this->category->items;

        foreach ($this->items as $item) {
            $this->counts[$item->id] = $item->repeat_count;
        }

        // Restore today's completion state.
        $log = $this->todayLog();
        if ($log && $log->completed_at) {
            $this->finished = true;
            $this->completed = $this->items->pluck('id')->all();
            foreach ($this->items as $item) {
                $this->counts[$item->id] = 0;
            }
        }
    }

    /** Called (from Alpine) when an item's counter reaches zero. */
    public function markComplete(int $itemId): void
    {
        if ($this->finished || in_array($itemId, $this->completed, true)) {
            return;
        }
        if (! $this->items->firstWhere('id', $itemId)) {
            return;
        }

        $this->completed[] = $itemId;
        $this->counts[$itemId] = 0;
        $this->persistProgress();

        if (count($this->completed) >= $this->items->count()) {
            $this->finish();
        }
    }

    /** Called (from Alpine) when an item is reset. */
    public function undo(int $itemId): void
    {
        if ($this->finished) {
            return;
        }
        $item = $this->items->firstWhere('id', $itemId);
        if ($item) {
            $this->counts[$itemId] = $item->repeat_count;
            $this->completed = array_values(array_diff($this->completed, [$itemId]));
            $this->persistProgress();
        }
    }

    protected function finish(): void
    {
        $this->finished = true;

        $log = $this->todayLog(create: true);
        $log->update([
            'completed_count' => $this->items->count(),
            'total' => $this->items->count(),
            'completed_at' => now(),
        ]);

        $user = auth()->user();
        $date = Carbon::now($user->timezone ?: config('app.timezone'));
        app(Gamification::class)->award($user, 'azkar', 8, "azkar:{$this->category->key}:".$date->toDateString(), $date);
        app(Streaks::class)->touch($user, 'azkar', $date);
        app(Streaks::class)->touch($user, 'overall', $date);
    }

    protected function persistProgress(): void
    {
        if ($this->finished) {
            return;
        }
        $this->todayLog(create: true)->update([
            'completed_count' => count($this->completed),
            'total' => $this->items->count(),
        ]);
    }

    protected function todayLog(bool $create = false)
    {
        $user = auth()->user();
        $date = Carbon::now($user->timezone ?: config('app.timezone'))->toDateString();

        // Look up with whereDate so a datetime-serialized value still matches.
        $log = $user->azkarLogs()
            ->where('category_id', $this->category->id)
            ->whereDate('date', $date)
            ->first();

        if (! $log && $create) {
            $log = $user->azkarLogs()->create([
                'category_id' => $this->category->id,
                'date' => $date,
                'total' => $this->items->count(),
            ]);
        }

        return $log;
    }

    public function progressPercent(): int
    {
        $total = max(1, $this->items->count());

        return (int) round(count($this->completed) / $total * 100);
    }

    public function render()
    {
        return view('livewire.azkar-session');
    }
}
