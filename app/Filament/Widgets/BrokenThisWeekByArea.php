<?php

namespace App\Filament\Widgets;

use App\Models\Report;
use Filament\Support\ArrayRecord;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;

class BrokenThisWeekByArea extends TableWidget
{
    protected static ?string $heading = 'Broken this week, by area';

    protected static ?int $sort = 2;

    protected int|string|array $columnSpan = 'full';

    public function table(Table $table): Table
    {
        return $table
            ->records(fn (): array => $this->areaCounts())
            ->columns([
                TextColumn::make('resolved_area')
                    ->label('Area'),
                TextColumn::make('reports')
                    ->label('Reports')
                    ->badge()
                    ->color('primary'),
                TextColumn::make('top_issue')
                    ->label('Most reported'),
                TextColumn::make('needs_attention')
                    ->label('Low confidence')
                    ->badge()
                    ->color(fn (int $state): string => $state > 0 ? 'danger' : 'gray')
                    ->tooltip('Reports the pipeline could not route confidently. A high count here usually means the gazetteer is thin for this area.'),
            ])
            ->paginated(false)
            ->emptyStateHeading('No reports in the last seven days')
            ->emptyStateDescription('Areas appear here as reports come in, ranked by volume.')
            ->emptyStateIcon('heroicon-o-map');
    }

    /**
     * Reports from the last seven days, clustered by the area the gazetteer
     * resolved them into. resolved_area is a denormalised name string set by
     * Report::resolveLocation(), so this needs no join.
     *
     * @return array<string, array<string, mixed>>
     */
    private function areaCounts(): array
    {
        $reports = Report::query()
            ->where('created_at', '>=', now()->subDays(7))
            ->whereNotNull('resolved_area')
            ->get(['resolved_area', 'issue_type', 'routing_confidence']);

        return $reports
            ->groupBy('resolved_area')
            ->map(fn ($group, string $area): array => [
                ArrayRecord::getKeyName() => $area,
                'resolved_area' => $area,
                'reports' => $group->count(),
                'top_issue' => $this->describeTopIssue($group),
                'needs_attention' => $group
                    ->whereIn('routing_confidence', ['low', 'needs_human_review'])
                    ->count(),
            ])
            ->sortByDesc('reports')
            ->all();
    }

    /**
     * @param  Collection<int, Report>  $group
     */
    private function describeTopIssue(Collection $group): string
    {
        $counts = $group->whereNotNull('issue_type')->countBy('issue_type')->sortDesc();

        if ($counts->isEmpty()) {
            return 'Unclassified';
        }

        $label = Str::headline((string) $counts->keys()->first());

        return "{$label} ({$counts->first()})";
    }
}
