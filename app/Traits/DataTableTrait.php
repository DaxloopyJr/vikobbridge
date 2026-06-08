<?php

namespace App\Traits;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;

trait DataTableTrait
{
    /**
     * Process DataTables server-side request manually (no external package needed)
     */
    protected function processDataTable(Request $request, Builder $query, array $searchableColumns = [], array $rawColumns = []): array
    {
        $draw = (int) $request->input('draw', 1);
        $start = (int) $request->input('start', 0);
        $length = (int) $request->input('length', 25);
        $search = $request->input('search.value', '');
        $order = $request->input('order', []);

        // Clone for count
        try {
            $recordsTotal = (clone $query)->count();
        } catch (\Exception $e) {
            $recordsTotal = 0;
        }

        // Apply search
        if ($search && !empty($searchableColumns)) {
            $query->where(function ($q) use ($search, $searchableColumns) {
                foreach ($searchableColumns as $i => $column) {
                    if (str_contains($column, '.')) {
                        // Handle relation.column searches
                        $parts = explode('.', $column);
                        $relation = $parts[0];
                        $col = $parts[1];
                        $method = $i === 0 ? 'whereHas' : 'orWhereHas';
                        $q->$method($relation, function ($sq) use ($search, $col) {
                            $sq->where($col, 'like', "%{$search}%");
                        });
                    } else {
                        $method = $i === 0 ? 'where' : 'orWhere';
                        $q->$method($column, 'like', "%{$search}%");
                    }
                }
            });
        }

        try {
            $recordsFiltered = (clone $query)->count();
        } catch (\Exception $e) {
            $recordsFiltered = 0;
        }

        // Apply ordering
        if (!empty($order)) {
            $orderColumnIndex = $order[0]['column'] ?? 0;
            $orderDir = $order[0]['dir'] ?? 'desc';
            $columns = $request->input('columns', []);
            if (isset($columns[$orderColumnIndex]['data'])) {
                $orderColumn = $columns[$orderColumnIndex]['data'];
                $orderColumn = $this->mapOrderColumn($orderColumn);
                if ($orderColumn) {
                    $query->orderBy($orderColumn, $orderDir);
                }
            }
        } else {
            $query->orderBy('id', 'desc');
        }

        // Apply pagination
        try {
            $data = $query->offset($start)->limit($length)->get();
        } catch (\Exception $e) {
            $data = collect();
        }

        return [
            'draw' => $draw,
            'recordsTotal' => $recordsTotal,
            'recordsFiltered' => $recordsFiltered,
            'data' => $data,
        ];
    }

    /**
     * Map DataTables column names to database column names
     */
    protected function mapOrderColumn(string $column): ?string
    {
        $map = [
            'id' => 'id',
            'name' => 'name',
            'first_name' => 'first_name',
            'last_name' => 'last_name',
            'email' => 'email',
            'phone_number' => 'phone_number',
            'status' => 'status',
            'created_at' => 'created_at',
            'updated_at' => 'updated_at',
            'amount' => 'amount',
            'loan_amount' => 'loan_amount',
            'payment_date' => 'payment_date',
            'collection_date' => 'collection_date',
            'year' => 'year',
            'registration_number' => 'registration_number',
            'fund_type' => 'fund_type',
            'rate_type' => 'rate_type',
            'rate_percentage' => 'rate_percentage',
            'member_number' => 'member_number',
            'join_date' => 'join_date',
            'expense_date' => 'expense_date',
            'description' => 'description',
            'price' => 'price',
            'duration_days' => 'duration_days',
            'is_active' => 'is_active',
            'member' => 'id',
            'plan' => 'id',
            'fund' => 'id',
            'type' => 'id',
            'rate' => 'id',
            'channel' => 'id',
            'actions' => null,
        ];

        return $map[$column] ?? 'id';
    }

    /**
     * Helper to get current group ID from session with fallback
     */
    protected function currentGroupId(): ?int
    {
        $groupId = session('current_group_id');

        if (!$groupId && auth()->check() && !auth()->user()->isSuperAdmin()) {
            // Try to get from user's primary group
            $primaryGroup = auth()->user()->primaryGroup();
            if ($primaryGroup) {
                $groupId = $primaryGroup->id;
                session(['current_group_id' => $groupId]);
            }
        }

        return $groupId ? (int) $groupId : null;
    }
}
