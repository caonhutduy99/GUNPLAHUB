@extends('layouts.app')

@section('title', 'Nhật ký hệ thống - GunplaHub')

@section('content')
<h3 class="mb-1">Nhật ký hệ thống</h3>
<p class="text-muted small">Ghi lại ai làm gì, lúc nào, ở đâu. Nhật ký chỉ được ghi thêm, không thể sửa hay xóa.</p>

<form method="GET" action="{{ route('admin.audit.index') }}" class="row g-2 mb-3">
    <div class="col-md-3">
        <input type="text" name="q" value="{{ request('q') }}" class="form-control" placeholder="Tìm trong nội dung (ví dụ: #12, Blacklist)">
    </div>
    <div class="col-md-3">
        <select name="user_id" class="form-select">
            <option value="">-- Tất cả người dùng --</option>
            @foreach($users as $user)
                <option value="{{ $user->id }}" @selected(request('user_id') == $user->id)>
                    {{ $user->full_name }}{{ $user->role === 'admin' ? ' (admin)' : '' }}
                </option>
            @endforeach
        </select>
    </div>
    <div class="col-md-2"><input type="date" name="from" value="{{ request('from') }}" class="form-control"></div>
    <div class="col-md-2"><input type="date" name="to" value="{{ request('to') }}" class="form-control"></div>
    <div class="col-md-2 d-flex gap-1">
        <button type="submit" class="btn btn-secondary flex-fill">Lọc</button>
        <a href="{{ route('admin.audit.index') }}" class="btn btn-outline-secondary">Xóa lọc</a>
    </div>
</form>

<div class="card shadow-sm">
    <div class="table-responsive">
        <table class="table table-hover mb-0 align-middle">
            <thead class="table-light">
                <tr>
                    <th style="width: 160px;">Thời gian</th>
                    <th style="width: 190px;">Người thực hiện</th>
                    <th>Hành động</th>
                    <th style="width: 130px;">IP</th>
                    <th style="width: 150px;">Thiết bị</th>
                </tr>
            </thead>
            <tbody>
                @forelse($logs as $log)
                    <tr>
                        <td class="small">{{ $log->timestamp->format('d/m/Y H:i:s') }}</td>
                        <td>
                            @if($log->user)
                                {{ $log->user->full_name }}
                                @if($log->user->role === 'admin')<span class="badge text-bg-dark">admin</span>@endif
                            @else
                                <span class="text-muted">Hệ thống</span>
                            @endif
                        </td>
                        <td>{{ $log->action }}</td>
                        <td class="small">{{ $log->ip_address ?: '—' }}</td>
                        <td class="small">{{ $log->device_label }}</td>
                    </tr>
                @empty
                    <tr><td colspan="5" class="text-center text-muted py-4">Không có bản ghi nào.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<div class="mt-3">
    {{ $logs->links('pagination::bootstrap-5') }}
</div>
@endsection
