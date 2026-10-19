@extends('layouts.app')

@section('title', 'مقارنة البرامج')

@section('content')
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('programs.index') }}">البرامج</a></li>
            <li class="breadcrumb-item active" aria-current="page">المقارنة</li>
        </ol>
    </nav>

    <h2 class="mb-1">مقارنة البرامج</h2>
    <p class="text-muted">
        الصفوف المظللة بالأصفر هي المعايير التي تختلف بين البرامج. الاختلاف لا يعني أن برنامجًا أفضل من غيره،
        و«غير متوفر» تعني أن القيمة غير مسجّلة وليست صفرًا.
    </p>

    {{-- جدول المعايير --}}
    <div class="card shadow-sm mb-4">
        <div class="table-responsive">
            <table class="table table-fit mb-0">
                <thead class="table-light">
                    <tr>
                        <th style="width: 20%">المعيار</th>
                        @foreach ($columns as $column)
                            <th>
                                <a href="{{ route('programs.show', $column['program']) }}" class="text-decoration-none">{{ $column['program']->name }}</a>
                                <div class="small text-muted fw-normal">{{ $column['program']->university->name }}</div>
                                @if ($loop->first)
                                    <span class="badge text-bg-info fw-normal">الأساس في تحليل الفرق</span>
                                @endif
                            </th>
                        @endforeach
                    </tr>
                </thead>
                <tbody>
                    @foreach ($rows as $row)
                        <tr class="{{ $row['differs'] ? 'table-warning' : '' }}">
                            <th class="fw-normal">
                                {{ $row['label'] }}
                                @if ($row['differs'])
                                    <span class="badge text-bg-warning">يختلف</span>
                                @endif
                            </th>
                            @foreach ($row['display'] as $value)
                                <td>
                                    @if ($value === null)
                                        <span class="text-muted">غير متوفر</span>
                                    @else
                                        {{ $value }}
                                    @endif
                                </td>
                            @endforeach
                        </tr>
                    @endforeach

                    <tr>
                        <th class="fw-normal">التكلفة التقديرية</th>
                        @foreach ($columns as $column)
                            <td>@include('partials.cost-cell', ['cost' => $column['cost']])</td>
                        @endforeach
                    </tr>

                    <tr>
                        <th class="fw-normal">المرافق والخدمات المتاحة</th>
                        @foreach ($columns as $column)
                            <td>
                                @forelse ($column['facilities'] as $facility)
                                    <span class="badge text-bg-light border">{{ $facility->name }}</span>
                                @empty
                                    <span class="text-muted">غير متوفر</span>
                                @endforelse
                            </td>
                        @endforeach
                    </tr>

                    <tr>
                        <th class="fw-normal">حالة البيانات</th>
                        @foreach ($columns as $column)
                            <td>@include('partials.data-status', ['status' => $column['program']->data_status])</td>
                        @endforeach
                    </tr>
                </tbody>
            </table>
        </div>
    </div>

    {{-- تفصيل التكلفة حسب الفئة --}}
    <div class="card shadow-sm mb-4">
        <div class="card-header bg-white fw-semibold">تفصيل التكلفة حسب الفئة</div>
        <div class="table-responsive">
            <table class="table table-fit mb-0">
                <thead class="table-light">
                    <tr>
                        <th style="width: 20%">الفئة</th>
                        @foreach ($columns as $column)
                            <th>
                                {{ $column['program']->name }}
                                <div class="small text-muted fw-normal">{{ $column['program']->university->name }}</div>
                            </th>
                        @endforeach
                    </tr>
                </thead>
                <tbody>
                    @forelse ($categories as $category)
                        <tr>
                            <th class="fw-normal">{{ \App\Support\Labels::category($category) }}</th>
                            @foreach ($columns as $column)
                                <td>
                                    @if (isset($column['by_category'][$category]))
                                        {{ number_format($column['by_category'][$category]) }} {{ $column['cost']['currency'] }}
                                    @else
                                        <span class="text-muted">غير متوفر</span>
                                    @endif
                                </td>
                            @endforeach
                        </tr>
                    @empty
                        <tr><td colspan="{{ $columns->count() + 1 }}" class="text-center text-muted py-3">لا توجد بنود رسوم متوفرة لهذه البرامج.</td></tr>
                    @endforelse
                </tbody>
                <tfoot>
                    <tr class="table-primary">
                        <th>الإجمالي التقديري</th>
                        @foreach ($columns as $column)
                            <th>@include('partials.cost-cell', ['cost' => $column['cost']])</th>
                        @endforeach
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>

    {{-- تحليل فرق التكلفة --}}
    <h4 class="mb-3">تحليل فرق التكلفة</h4>
    <p class="text-muted small">
        يقارن كل برنامج بالبرنامج الأساس ({{ $base['program']->name }} — {{ $base['program']->university->name }}).
        يوضح التحليل مصدر الفرق الحسابي بين الرسوم فقط، ولا يعني أن البرنامج الأغلى أعلى جودة.
    </p>

    @foreach ($analyses as $item)
        @php $result = $item['result']; @endphp

        <div class="card shadow-sm mb-4">
            <div class="card-header bg-white fw-semibold">
                {{ $item['program']->name }} ({{ $item['program']->university->name }})
                مقابل
                {{ $base['program']->name }} ({{ $base['program']->university->name }})
            </div>

            <div class="card-body">
                @if (! $result['comparable'])
                    <div class="alert alert-secondary mb-0">{{ $result['reason'] }}</div>
                @elseif (! $result['has_comparable'])
                    <div class="alert alert-secondary mb-0">لا توجد فئة رسوم متوفرة في كلا البرنامجين، لذلك لا يمكن حساب الفرق.</div>
                @else
                    @php $diff = $result['comparable_diff']; @endphp

                    <p class="fs-5 mb-3">
                        @if ($diff > 0)
                            «{{ $item['program']->name }}» أغلى من الأساس بمقدار
                            <strong>{{ number_format($diff) }} {{ $base['cost']['currency'] }}</strong>
                        @elseif ($diff < 0)
                            «{{ $item['program']->name }}» أرخص من الأساس بمقدار
                            <strong>{{ number_format(abs($diff)) }} {{ $base['cost']['currency'] }}</strong>
                        @else
                            لا يوجد فرق في الفئات المتوفرة في كلا البرنامجين.
                        @endif
                    </p>

                    @unless ($result['is_reliable'])
                        <div class="alert alert-warning py-2">
                            إجمالي أحد البرنامجين (أو كليهما) غير مكتمل، لذلك الفرق أدناه يخص الفئات المتوفرة فقط
                            وقد لا يعكس الفرق الفعلي.
                        </div>
                    @endunless

                    @if (! empty($result['skipped']))
                        <div class="alert alert-secondary py-2">
                            فئات متوفرة في أحد البرنامجين فقط ولم تدخل في الفرق:
                            {{ collect($result['skipped'])->map(fn ($c) => \App\Support\Labels::category($c))->implode('، ') }}.
                        </div>
                    @endif

                    <div class="table-responsive">
                        <table class="table table-fit mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th>الفئة</th>
                                    <th>الأساس</th>
                                    <th>هذا البرنامج</th>
                                    <th>الفرق</th>
                                    <th>مساهمة الفئة في الفرق</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($result['rows'] as $row)
                                    <tr>
                                        <td>{{ \App\Support\Labels::category($row['category']) }}</td>
                                        <td>
                                            @if ($row['base'] !== null)
                                                {{ number_format($row['base']) }}
                                            @else
                                                <span class="text-muted">غير متوفر</span>
                                            @endif
                                        </td>
                                        <td>
                                            @if ($row['other'] !== null)
                                                {{ number_format($row['other']) }}
                                            @else
                                                <span class="text-muted">غير متوفر</span>
                                            @endif
                                        </td>
                                        <td>
                                            @if ($row['diff'] !== null)
                                                <span dir="ltr">{{ $row['diff'] > 0 ? '+' : '' }}{{ number_format($row['diff']) }}</span>
                                            @else
                                                <span class="text-muted">غير قابل للمقارنة</span>
                                            @endif
                                        </td>
                                        <td>
                                            @if ($row['share'] !== null)
                                                <span dir="ltr">{{ $row['share'] }}%</span>
                                            @else
                                                <span class="text-muted">—</span>
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    <p class="text-muted small mt-2 mb-0">
                        مساهمة الفئة = فرق الفئة ÷ إجمالي الفرق. قد تتجاوز 100% أو تكون سالبة عندما تعوّض فئة فرق فئة أخرى.
                    </p>
                @endif
            </div>
        </div>
    @endforeach

    <p class="text-muted small">
        هذه المقارنة استرشادية وتعتمد على البيانات المسجّلة فقط، ولا تُعد تقييمًا رسميًا لجودة أي برنامج.
    </p>
@endsection
