<?php

namespace App\Filament\Widgets;

use App\Models\Authority;
use App\Models\GazetteerNode;
use App\Models\Report;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class RoutingHealth extends StatsOverviewWidget
{
    protected static ?int $sort = 1;

    protected function getStats(): array
    {
        $total = Report::query()->count();

        $needsReview = Report::query()
            ->whereIn('routing_confidence', ['low', 'needs_human_review'])
            ->count();

        $unverified = Authority::query()
            ->where('citizen_visible', false)
            ->count();

        return [
            Stat::make('Reports routed', (string) $total)
                ->description($total === 0 ? 'Waiting on the first report' : 'All time')
                ->color('primary'),

            Stat::make('Need a human', (string) $needsReview)
                ->description('Low confidence or unresolved jurisdiction')
                ->color($needsReview > 0 ? 'warning' : 'success'),

            Stat::make('Authorities without a citizen channel', (string) $unverified)
                ->description('Of '.Authority::query()->count().' on file; these get a KMC 1339 escalation')
                ->color($unverified > 0 ? 'danger' : 'success'),

            Stat::make('Gazetteer nodes', (string) GazetteerNode::query()->count())
                ->description('Places the resolver can match a report against')
                ->color('gray'),
        ];
    }
}
