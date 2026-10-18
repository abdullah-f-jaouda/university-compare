@extends('layouts.app')

@section('title', $university->name)

@section('content')
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('universities.index') }}">الجامعات</a></li>
            <li class="breadcrumb-item active" aria-current="page">{{ $university->name }}</li>
        </ol>
    </nav>

    <div class="card shadow-sm mb-4">
        <div class="card-body">
            <div class="d-flex justify-content-between align-items-start">
                <h2 class="mb-1">{{ $university->name }}</h2>
                @include('partials.data-status', ['status' => $university->data_status])
            </div>
            <p class="text-muted mb-2">{{ $university->city }}</p>
            <p class="mb-0">{{ $university->description }}</p>
            @if ($university->website)
                <p class="mt-2 mb-0"><a href="{{ $university->website }}" target="_blank" rel="noopener">الموقع الإلكتروني</a></p>
            @endif
            @if ($university->source_note)
                <p class="text-muted small mt-2 mb-0">ملاحظة المصدر: {{ $university->source_note }}</p>
            @endif
        </div>
    </div>

    <h4 class="mb-3">البرامج المتاحة</h4>
    <div class="card shadow-sm mb-4">
        <div class="table-responsive">
            <table class="table table-hover table-fit mb-0">
                <thead class="table-light">
                    <tr>
                        <th>البرنامج</th>
                        <th>الدرجة</th>
                        <th>المدة</th>
                        <th>التكلفة التقديرية</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($programs as $program)
                        <tr>
                            <td>{{ $program->name }}</td>
                            <td>{{ \App\Support\Labels::degree($program->degree_type) }}</td>
                            <td>{{ \App\Support\Labels::orNA($program->duration_years !== null ? (float) $program->duration_years : null, ' سنة') }}</td>
                            <td>@include('partials.cost-cell', ['cost' => $program->cost])</td>
                            <td><a href="{{ route('programs.show', $program) }}" class="btn btn-sm btn-outline-primary">التفاصيل</a></td>
                        </tr>
                    @empty
                        <tr><td colspan="5" class="text-center text-muted py-3">لا توجد برامج مسجّلة لهذه الجامعة.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <h4 class="mb-3">المرافق والخدمات</h4>
    <div class="card shadow-sm">
        <ul class="list-group list-group-flush">
            @forelse ($facilities as $facility)
                <li class="list-group-item d-flex justify-content-between align-items-center">
                    <span>
                        {{ $facility->name }}
                        <span class="badge text-bg-light border">{{ \App\Support\Labels::facilityType($facility->type) }}</span>
                    </span>
                    <span>
                        @if ($facility->is_available)
                            <span class="badge text-bg-success">متاح</span>
                        @else
                            <span class="badge text-bg-secondary">غير متاح</span>
                        @endif
                        @include('partials.data-status', ['status' => $facility->data_status])
                    </span>
                </li>
            @empty
                <li class="list-group-item text-muted">لا توجد مرافق أو خدمات مسجّلة.</li>
            @endforelse
        </ul>
    </div>
@endsection
