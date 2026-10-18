@extends('layouts.app')

@section('title', 'الجامعات')

@section('content')
    <h2 class="mb-4">الجامعات الخاصة في بنغازي</h2>

    <div class="row g-3">
        @forelse ($universities as $university)
            <div class="col-12 col-md-6">
                <div class="card shadow-sm h-100">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-start">
                            <h5 class="card-title mb-1">{{ $university->name }}</h5>
                            @include('partials.data-status', ['status' => $university->data_status])
                        </div>
                        <p class="text-muted small mb-2">{{ $university->city }}</p>
                        <p class="card-text">{{ $university->description }}</p>
                        <div class="d-flex justify-content-between align-items-center">
                            <span class="text-muted small">{{ $university->programs_count }} برنامج</span>
                            <a href="{{ route('universities.show', $university) }}" class="btn btn-sm btn-primary">عرض التفاصيل</a>
                        </div>
                    </div>
                </div>
            </div>
        @empty
            <div class="col-12"><div class="alert alert-info">لا توجد جامعات مسجّلة حاليًا.</div></div>
        @endforelse
    </div>
@endsection
