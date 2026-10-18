@extends('layouts.app')

@section('title', 'Tin nhắn liên hệ - GunplaHub')

@section('content')
<h3 class="mb-3">Tin nhắn liên hệ</h3>

<div class="card shadow-sm">
    <div class="table-responsive">
        <table class="table table-hover mb-0 align-top">
            <thead class="table-light">
                <tr>
                    <th style="width: 150px;">Thời gian</th>
                    <th style="width: 200px;">Người gửi</th>
                    <th>Nội dung</th>
                </tr>
            </thead>
            <tbody>
                @forelse($messages as $item)
                    <tr>
                        <td class="small">{{ $item->created_at->format('d/m/Y H:i') }}</td>
                        <td>
                            <div class="fw-semibold">{{ $item->name }}</div>
                            <div class="small"><a href="mailto:{{ $item->email }}" class="text-reset">{{ $item->email }}</a></div>
                            <div class="small"><a href="tel:{{ $item->phone }}" class="text-reset">{{ $item->phone }}</a></div>
                        </td>
                        <td style="white-space: pre-line;">{{ $item->message }}</td>
                    </tr>
                @empty
                    <tr><td colspan="3" class="text-center text-muted py-3">Chưa có tin nhắn nào.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<div class="mt-3">
    {{ $messages->links('pagination::bootstrap-5') }}
</div>
@endsection
