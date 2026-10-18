@extends('layouts.app')

@section('title', $program->name . ' — ' . $program->university->name)

@section('content')
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('programs.index') }}">البرامج</a></li>
            <li class="breadcrumb-item"><a href="{{ route('universities.show', $program->university) }}">{{ $program->university->name }}</a></li>
            <li class="breadcrumb-item active" aria-current="page">{{ $program->name }}</li>
        </ol>
    </nav>

    {{-- معلومات البرنامج --}}
    <div class="card shadow-sm mb-4">
        <div class="card-body">
            <div class="d-flex justify-content-between align-items-start">
                <div>
                    <h2 class="mb-1">{{ $program->name }}</h2>
                    <p class="text-muted mb-2">{{ $program->university->name }}</p>
                </div>
                @include('partials.data-status', ['status' => $program->data_status])
            </div>

            <p>{{ $program->description }}</p>

            <div class="row g-3 mt-1">
                <div class="col-6 col-md-3">
                    <div class="text-muted small">الدرجة</div>
                    <div class="fw-semibold">{{ \App\Support\Labels::degree($program->degree_type) }}</div>
                </div>
                <div class="col-6 col-md-3">
                    <div class="text-muted small">المدة</div>
                    <div class="fw-semibold">{{ \App\Support\Labels::orNA($program->duration_years !== null ? (float) $program->duration_years : null, ' سنة') }}</div>
                </div>
                <div class="col-6 col-md-3">
                    <div class="text-muted small">الساعات المعتمدة</div>
                    <div class="fw-semibold">{{ \App\Support\Labels::orNA($program->total_credit_hours) }}</div>
                </div>
                <div class="col-6 col-md-3">
                    <div class="text-muted small">نسبة الجانب العملي</div>
                    <div class="fw-semibold">{{ \App\Support\Labels::orNA($program->practical_percentage, '%') }}</div>
                </div>
            </div>
        </div>
    </div>

    {{-- متطلبات القبول --}}
    <div class="card shadow-sm mb-4">
        <div class="card-header bg-white fw-semibold">متطلبات القبول</div>
        <div class="card-body">
            <p class="mb-1">
                الحد الأدنى للنسبة:
                <strong>{{ $program->min_admission_percentage !== null ? number_format((float) $program->min_admission_percentage, 1) . '%' : 'غير متوفر' }}</strong>
            </p>
            @if ($program->admission_notes)
                <p class="text-muted mb-0">{{ $program->admission_notes }}</p>
            @endif
        </div>
    </div>

    {{-- حاسبة التكلفة --}}
    <div class="card shadow-sm mb-4">
        <div class="card-header bg-white fw-semibold">التكلفة التقديرية للبرنامج</div>
        <div class="card-body">
            @if ($cost['lines'] === [])
                <div class="alert alert-secondary mb-0">لا توجد بنود رسوم متوفرة لهذا البرنامج، لذلك لا يمكن حساب التكلفة.</div>
            @else
                <p class="text-muted small">
                    الافتراضات:
                    @if ($cost['years'] !== null)
                        المدة {{ $cost['years'] }} سنة ({{ $cost['semesters'] }} فصل)،
                    @else
                        مدة الدراسة غير متوفرة،
                    @endif
                    العملة {{ $cost['currency'] }}. الرسم لكل فصل يُضرب في عدد الفصول، والرسم السنوي يُحتسب لكل سنة دراسية بدأت، ورسم المرة الواحدة يُحتسب مرة وحدة.
                </p>

                @if ($cost['is_illustrative'])
                    <div class="alert alert-warning py-2">الأرقام التالية تجريبية لأغراض العرض، وليست رسوم الجامعة الرسمية.</div>
                @endif

                @if (! empty($cost['missing']))
                    <div class="alert alert-warning py-2">
                        بنود غير متوفرة:
                        {{ collect($cost['missing'])->map(fn ($c) => \App\Support\Labels::category($c))->implode('، ') }}.
                        لم تُعامل كصفر، لذلك الإجمالي المعروض <strong>تقدير غير مكتمل</strong>.
                    </div>
                @endif

                <div class="table-responsive">
                    <table class="table table-fit">
                        <thead class="table-light">
                            <tr>
                                <th>البند</th>
                                <th>الفئة</th>
                                <th>المبلغ</th>
                                <th>التكرار</th>
                                <th>عدد المرات</th>
                                <th>المجموع الفرعي</th>
                                <th>حالة البيانات</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($cost['lines'] as $line)
                                <tr>
                                    <td>{{ $line['label'] ?? \App\Support\Labels::category($line['category']) }}</td>
                                    <td>{{ \App\Support\Labels::category($line['category']) }}</td>
                                    <td>{{ number_format($line['amount']) }} {{ $cost['currency'] }}</td>
                                    <td>{{ \App\Support\Labels::frequency($line['frequency']) }}</td>
                                    <td>{{ $line['times'] ?? 'غير متوفر' }}</td>
                                    <td>{{ $line['subtotal'] !== null ? number_format($line['subtotal']) . ' ' . $cost['currency'] : 'غير متوفر' }}</td>
                                    <td>@include('partials.data-status', ['status' => $line['data_status']])</td>
                                </tr>
                            @endforeach
                        </tbody>
                        <tfoot>
                            <tr class="table-primary">
                                <th colspan="5">الإجمالي التقديري{{ $cost['is_complete'] ? '' : ' (غير مكتمل)' }}</th>
                                <th colspan="2">
                                    @if ($cost['total'] !== null)
                                        {{ number_format($cost['total']) }} {{ $cost['currency'] }}
                                    @else
                                        غير متوفر
                                    @endif
                                </th>
                            </tr>
                        </tfoot>
                    </table>
                </div>

                @if (! empty($cost['breakdown']) && $cost['total'])
                    <h6 class="mt-4">مساهمة كل فئة في الإجمالي</h6>
                    @foreach ($cost['breakdown'] as $row)
                        <div class="mb-2">
                            <div class="d-flex justify-content-between small">
                                <span>{{ \App\Support\Labels::category($row['category']) }}</span>
                                <span>{{ number_format($row['subtotal']) }} {{ $cost['currency'] }} ({{ $row['percent'] }}%)</span>
                            </div>
                            <div class="progress" role="progressbar" aria-valuenow="{{ $row['percent'] }}" aria-valuemin="0" aria-valuemax="100">
                                <div class="progress-bar" style="width: {{ $row['percent'] }}%"></div>
                            </div>
                        </div>
                    @endforeach
                @endif
            @endif
        </div>
    </div>

    {{-- الخطة الدراسية --}}
    <div class="card shadow-sm mb-4">
        <div class="card-header bg-white fw-semibold">المقررات</div>
        <div class="table-responsive">
            <table class="table table-fit mb-0">
                <thead class="table-light">
                    <tr>
                        <th>الفصل</th>
                        <th>المقرر</th>
                        <th>الساعات</th>
                        <th>النوع</th>
                        <th>حالة البيانات</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($courses as $course)
                        <tr>
                            <td>{{ \App\Support\Labels::orNA($course->semester) }}</td>
                            <td>{{ $course->name }}</td>
                            <td>{{ \App\Support\Labels::orNA($course->credit_hours) }}</td>
                            <td>{{ \App\Support\Labels::courseType($course->course_type) }}</td>
                            <td>@include('partials.data-status', ['status' => $course->data_status])</td>
                        </tr>
                    @empty
                        <tr><td colspan="5" class="text-center text-muted py-3">لا توجد مقررات مسجّلة لهذا البرنامج.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <p class="text-muted small">
        هذه المعلومات استرشادية، ولا تُعد ضمانًا للقبول أو تقييمًا رسميًا لجودة البرنامج.
    </p>
@endsection
