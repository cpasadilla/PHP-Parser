<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\OrderUpdateLog;
use App\Models\OrderDeleteLog;
use App\Models\Order;
use Illuminate\Pagination\Paginator;
use App\Models\User; // Add this import

class HistoryController extends Controller
{
    public function index(Request $request)
    {
        $nameFilter = $request->input('name');
        $updatedByFilter = $request->input('updated_by');
        $deletedByFilter = $request->input('deleted_by');
        $restoreStatusFilter = $request->input('restore_status');
        $fieldNameFilter = $request->input('field_name');
        $actionTypeFilter = $request->input('action_type');
        $searchFilter = $request->input('search');
        $shipFilter = $request->input('ship');
        $voyageFilter = $request->input('voyage');

        // Fetch user activity logs from the sessions table with pagination and filtering
        $perPage = request('per_page', 10);
        if ($perPage == '999999') $perPage = 999999; // For "all"
        $sort = request('sort', 'desc'); // Default sort for timestamps
        $userActivities = DB::table('sessions')
            ->join('users', 'sessions.user_id', '=', 'users.id')
            ->select(DB::raw("CONCAT(users.fName, ' ', users.lName) as name"), 'sessions.ip_address', 'sessions.last_activity')
            ->when($nameFilter, function ($query, $nameFilter) {
                return $query->where(DB::raw("CONCAT(users.fName, ' ', users.lName)"), 'like', "%$nameFilter%");
            })
            ->orderBy('sessions.last_activity', 'desc')
            ->paginate($perPage, ['*'], 'userActivitiesPage');

        // Fetch order update logs with pagination and filtering
        $orderUpdateLogs = OrderUpdateLog::select('order_update_logs.*')
            ->join('orders', 'order_update_logs.order_id', '=', 'orders.id')
            ->select(
                'order_update_logs.*',
                'orders.shipNum as ship_name',
                'orders.voyageNum as voyage_number',
                'orders.orderId as bl_number'
            )
            ->when($updatedByFilter, function ($query, $updatedByFilter) {
                return $query->where('order_update_logs.updated_by', 'like', "%$updatedByFilter%");
            })
            ->when($fieldNameFilter, function ($query, $fieldNameFilter) {
                return $query->where('order_update_logs.field_name', $fieldNameFilter);
            })
            ->when($actionTypeFilter, function ($query, $actionTypeFilter) {
                return $query->where('order_update_logs.action_type', $actionTypeFilter);
            })
            ->when($searchFilter, function ($query, $searchFilter) {
                return $query->where('orders.orderId', 'like', "%$searchFilter%");
            })
            ->when($shipFilter, function ($query, $shipFilter) {
                return $query->where('orders.shipNum', $shipFilter);
            })
            ->when($voyageFilter, function ($query, $voyageFilter) {
                return $query->where('orders.voyageNum', $voyageFilter);
            })
            ->when($sort === 'bl_asc' || $sort === 'bl_desc', function ($query) use ($sort) {
                return $query->orderBy('orders.orderId', $sort === 'bl_asc' ? 'asc' : 'desc');
            })
            ->orderBy('order_update_logs.updated_at', 'desc')
            ->paginate($perPage, ['*'], 'orderUpdateLogsPage');

        // Fetch order delete logs with pagination and filtering
        $orderDeleteLogs = OrderDeleteLog::query()
            ->with(['restoredOrder' => function($query) {
                $query->select('id', 'shipNum', 'voyageNum');
            }])
            ->when($deletedByFilter, function ($query, $deletedByFilter) {
                return $query->where('deleted_by', 'like', "%$deletedByFilter%");
            })
            ->when($restoreStatusFilter, function ($query, $restoreStatusFilter) {
                if ($restoreStatusFilter === 'deleted') {
                    return $query->whereNull('restored_at');
                } elseif ($restoreStatusFilter === 'restored') {
                    return $query->whereNotNull('restored_at');
                }
            })
            ->when($sort === 'bl_asc' || $sort === 'bl_desc', function ($query) use ($sort) {
                return $query->orderBy('bl_number', $sort === 'bl_asc' ? 'asc' : 'desc');
            })
            ->orderBy('created_at', 'desc')
            ->paginate($perPage, ['*'], 'orderDeleteLogsPage');

        // Fetch all users for the filter dropdown
        $allUsers = User::select(DB::raw("CONCAT(fName, ' ', lName) as name"))->get();

        // Fetch all ships and voyages for the filter dropdowns
        $allShips = Order::distinct('shipNum')->pluck('shipNum');
        $allVoyages = Order::distinct('voyageNum')->pluck('voyageNum');

        return view('history.index', [
            'userActivities' => $userActivities,
            'orderUpdateLogs' => $orderUpdateLogs,
            'orderDeleteLogs' => $orderDeleteLogs,
            'allUsers' => $allUsers, // Pass all users to the view
            'allShips' => $allShips,
            'allVoyages' => $allVoyages,
        ]);
    }
}