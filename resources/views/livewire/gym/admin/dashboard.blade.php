<div>
    <x-slot name="header">
        <h2 class="text-2xl font-black text-white tracking-tight flex items-center gap-2">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-orange-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M11 3.055A9.001 9.001 0 1020.945 13H11V3.055z" />
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M20.488 9H15V3.512A9.025 9.025 0 0120.488 9z" />
            </svg>
            Panel de Control Administrativo
        </h2>
    </x-slot>

    <!-- Script loading for ApexCharts -->
    <script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>

    <!-- KPI Grid -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
        
        <!-- KPI Card: Total Members -->
        <div class="bg-zinc-900/40 backdrop-blur-md border border-zinc-800/80 rounded-2xl p-6 shadow-xl hover:border-orange-500/40 hover:shadow-orange-500/5 transition-all duration-300 group">
            <div class="flex items-center justify-between">
                <div>
                    <span class="text-xs font-bold uppercase tracking-wider text-zinc-500">Total Socios</span>
                    <h3 class="text-4xl font-extrabold text-white mt-1 group-hover:text-orange-400 transition-colors duration-300">
                        {{ $totalMembers }}
                    </h3>
                </div>
                <div class="w-12 h-12 rounded-xl bg-orange-500/10 border border-orange-500/20 flex items-center justify-center text-orange-500 shadow-inner">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                    </svg>
                </div>
            </div>
            <div class="mt-4 flex items-center text-xs text-zinc-400">
                <span class="text-emerald-500 font-bold mr-1">Activos e inactivos</span>
                <span>registrados en total</span>
            </div>
        </div>

        <!-- KPI Card: Active Memberships -->
        <div class="bg-zinc-900/40 backdrop-blur-md border border-zinc-800/80 rounded-2xl p-6 shadow-xl hover:border-orange-500/40 hover:shadow-orange-500/5 transition-all duration-300 group">
            <div class="flex items-center justify-between">
                <div>
                    <span class="text-xs font-bold uppercase tracking-wider text-zinc-500">Membresías Activas</span>
                    <h3 class="text-4xl font-extrabold text-white mt-1 group-hover:text-orange-400 transition-colors duration-300">
                        {{ $activeMemberships }}
                    </h3>
                </div>
                <div class="w-12 h-12 rounded-xl bg-amber-500/10 border border-amber-500/20 flex items-center justify-center text-amber-500 shadow-inner">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
            </div>
            <div class="mt-4 flex items-center text-xs text-zinc-400">
                @if($totalMembers > 0)
                    <span class="text-amber-500 font-bold mr-1">{{ round(($activeMemberships / $totalMembers) * 100) }}%</span>
                    <span>del total de socios registrados</span>
                @else
                    <span>Sin socios registrados</span>
                @endif
            </div>
        </div>

        <!-- KPI Card: Monthly Revenue -->
        <div class="bg-zinc-900/40 backdrop-blur-md border border-zinc-800/80 rounded-2xl p-6 shadow-xl hover:border-orange-500/40 hover:shadow-orange-500/5 transition-all duration-300 group">
            <div class="flex items-center justify-between">
                <div>
                    <span class="text-xs font-bold uppercase tracking-wider text-zinc-500">Recaudación (30d)</span>
                    <h3 class="text-4xl font-extrabold text-white mt-1 group-hover:text-emerald-400 transition-colors duration-300">
                        ${{ number_format($monthlyRevenue, 0, ',', '.') }}
                    </h3>
                </div>
                <div class="w-12 h-12 rounded-xl bg-emerald-500/10 border border-emerald-500/20 flex items-center justify-center text-emerald-500 shadow-inner">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
            </div>
            <div class="mt-4 flex items-center text-xs text-zinc-400">
                <span class="text-emerald-500 font-bold mr-1">Pagos completados</span>
                <span>a través de MercadoPago</span>
            </div>
        </div>

        <!-- KPI Card: Attendance Today -->
        <div class="bg-zinc-900/40 backdrop-blur-md border border-zinc-800/80 rounded-2xl p-6 shadow-xl hover:border-orange-500/40 hover:shadow-orange-500/5 transition-all duration-300 group">
            <div class="flex items-center justify-between">
                <div>
                    <span class="text-xs font-bold uppercase tracking-wider text-zinc-500">Asistencia Hoy</span>
                    <h3 class="text-4xl font-extrabold text-white mt-1 group-hover:text-blue-400 transition-colors duration-300">
                        {{ $attendanceToday }}
                    </h3>
                </div>
                <div class="w-12 h-12 rounded-xl bg-blue-500/10 border border-blue-500/20 flex items-center justify-center text-blue-500 shadow-inner">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z" />
                    </svg>
                </div>
            </div>
            <div class="mt-4 flex items-center text-xs text-zinc-400">
                <span class="text-blue-500 font-bold mr-1">Check-ins registrados</span>
                <span>durante el día de hoy</span>
            </div>
        </div>

    </div>

    <!-- Charts Section -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-8">
        
        <!-- Left: Monthly Revenue Line/Area Chart -->
        <div class="lg:col-span-2 bg-zinc-900/40 backdrop-blur-md border border-zinc-800/80 rounded-2xl p-6 shadow-xl">
            <h4 class="text-lg font-bold text-white mb-4 flex items-center gap-2">
                <span class="w-2.5 h-2.5 rounded-full bg-orange-500"></span>
                Historial de Ingresos Mensuales
            </h4>
            <div x-data="{
                labels: {{ json_encode($revenueTrendLabels) }},
                data: {{ json_encode($revenueTrendData) }},
                init() {
                    let options = {
                        chart: {
                            type: 'area',
                            height: 320,
                            toolbar: { show: false },
                            background: 'transparent',
                            foreColor: '#a1a1aa'
                        },
                        stroke: {
                            curve: 'smooth',
                            width: 3,
                            colors: ['#f97316']
                        },
                        fill: {
                            type: 'gradient',
                            gradient: {
                                shadeIntensity: 1,
                                opacityFrom: 0.45,
                                opacityTo: 0.02,
                                stops: [0, 100]
                            }
                        },
                        series: [{
                            name: 'Ingresos',
                            data: this.data
                        }],
                        xaxis: {
                            categories: this.labels,
                            axisBorder: { show: false },
                            axisTicks: { show: false }
                        },
                        yaxis: {
                            labels: {
                                formatter: function(val) {
                                    return '$' + val.toLocaleString();
                                }
                            }
                        },
                        grid: {
                            borderColor: '#27272a',
                            strokeDashArray: 4
                        },
                        colors: ['#f97316'],
                        tooltip: {
                            theme: 'dark'
                        }
                    };
                    let chart = new ApexCharts(this.$refs.revenueChart, options);
                    chart.render();
                }
            }" class="w-full">
                <div x-ref="revenueChart"></div>
            </div>
        </div>

        <!-- Right: Donut Chart - Plan Distribution -->
        <div class="bg-zinc-900/40 backdrop-blur-md border border-zinc-800/80 rounded-2xl p-6 shadow-xl">
            <h4 class="text-lg font-bold text-white mb-4 flex items-center gap-2">
                <span class="w-2.5 h-2.5 rounded-full bg-orange-500"></span>
                Distribución de Membresías
            </h4>
            
            @if(count($planCounts) > 0)
                <div x-data="{
                    labels: {{ json_encode($planLabels) }},
                    data: {{ json_encode($planCounts) }},
                    init() {
                        let options = {
                            chart: {
                                type: 'donut',
                                height: 320,
                                background: 'transparent',
                                foreColor: '#a1a1aa'
                            },
                            series: this.data,
                            labels: this.labels,
                            colors: ['#f97316', '#3b82f6', '#10b981', '#a855f7', '#f43f5e'],
                            stroke: { show: true, colors: ['#18181b'], width: 2 },
                            legend: {
                                position: 'bottom',
                                labels: { colors: '#a1a1aa' }
                            },
                            plotOptions: {
                                pie: {
                                    donut: {
                                        size: '72%',
                                        background: 'transparent',
                                        labels: {
                                            show: true,
                                            name: { show: true, color: '#a1a1aa' },
                                            value: {
                                                show: true,
                                                color: '#ffffff',
                                                formatter: function(val) {
                                                    return val + ' socios';
                                                }
                                            },
                                            total: {
                                                show: true,
                                                label: 'Total Activas',
                                                color: '#a1a1aa',
                                                formatter: function(w) {
                                                    return w.globals.seriesTotals.reduce((a, b) => a + b, 0) + ' socios';
                                                }
                                            }
                                        }
                                    }
                                }
                            },
                            tooltip: { theme: 'dark' }
                        };
                        let chart = new ApexCharts(this.$refs.donutChart, options);
                        chart.render();
                    }
                }" class="w-full h-full flex flex-col justify-center">
                    <div x-ref="donutChart"></div>
                </div>
            @else
                <div class="h-[320px] flex flex-col items-center justify-center text-zinc-500">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-12 w-12 text-zinc-650 mb-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                    </svg>
                    <span>Sin membresías activas para graficar</span>
                </div>
            @endif
        </div>

    </div>

    <!-- Lists Section (Recent Payments and Checkins) -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        
        <!-- Left: Recent Payments -->
        <div class="bg-zinc-900/40 backdrop-blur-md border border-zinc-800/80 rounded-2xl p-6 shadow-xl">
            <div class="flex items-center justify-between mb-4">
                <h4 class="text-lg font-bold text-white flex items-center gap-2">
                    <span class="w-2.5 h-2.5 rounded-full bg-emerald-500"></span>
                    Últimos Pagos Recibidos
                </h4>
                <a href="/g/{{ tenant('id') }}/admin/payments" class="text-xs font-bold text-orange-500 hover:text-orange-400">Ver todo</a>
            </div>
            
            <div class="overflow-x-auto">
                <table class="table table-xs w-full text-zinc-300">
                    <thead>
                        <tr class="border-b border-zinc-800 text-zinc-400 font-bold">
                            <th class="py-3">Socio</th>
                            <th class="py-3">Monto</th>
                            <th class="py-3">Método</th>
                            <th class="py-3">Fecha</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($recentPayments as $payment)
                            <tr class="border-b border-zinc-800/60 hover:bg-zinc-800/20 transition-all duration-150">
                                <td class="py-3">
                                    <div class="font-semibold text-white">{{ $payment->user->name ?? 'Usuario Eliminado' }}</div>
                                    <div class="text-[10px] text-zinc-500">{{ $payment->user->email ?? '' }}</div>
                                </td>
                                <td class="py-3 text-emerald-400 font-bold">${{ number_format($payment->amount, 0, ',', '.') }}</td>
                                <td class="py-3">
                                    <span class="badge badge-sm bg-zinc-800 border-zinc-700 text-zinc-300">
                                        {{ $payment->payment_method ?? 'MP' }}
                                    </span>
                                </td>
                                <td class="py-3 text-xs text-zinc-400">
                                    {{ $payment->created_at->diffForHumans() }}
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="text-center py-6 text-zinc-500">No se han registrado pagos recientes</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Right: Recent Check-ins -->
        <div class="bg-zinc-900/40 backdrop-blur-md border border-zinc-800/80 rounded-2xl p-6 shadow-xl">
            <div class="flex items-center justify-between mb-4">
                <h4 class="text-lg font-bold text-white flex items-center gap-2">
                    <span class="w-2.5 h-2.5 rounded-full bg-blue-500"></span>
                    Asistencia Reciente
                </h4>
                <a href="/g/{{ tenant('id') }}/admin/classes" class="text-xs font-bold text-orange-500 hover:text-orange-400">Ver todo</a>
            </div>

            <div class="overflow-x-auto">
                <table class="table table-xs w-full text-zinc-300">
                    <thead>
                        <tr class="border-b border-zinc-800 text-zinc-400 font-bold">
                            <th class="py-3">Socio</th>
                            <th class="py-3">Clase / Actividad</th>
                            <th class="py-3">Fecha</th>
                            <th class="py-3 text-right">Estado</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($recentCheckins as $checkin)
                            <tr class="border-b border-zinc-800/60 hover:bg-zinc-800/20 transition-all duration-150">
                                <td class="py-3">
                                    <div class="font-semibold text-white">{{ $checkin->user->name ?? 'Usuario Eliminado' }}</div>
                                    <div class="text-[10px] text-zinc-500">{{ $checkin->user->email ?? '' }}</div>
                                </td>
                                <td class="py-3">
                                    <div class="font-semibold text-zinc-200">
                                        {{ $checkin->gymClass->name ?? 'Acceso General' }}
                                    </div>
                                    <td class="py-3 text-xs text-zinc-400">
                                        {{ $checkin->created_at->diffForHumans() }}
                                    </td>
                                </td>
                                <td class="py-3 text-right">
                                    <span class="badge badge-sm {{ $checkin->status === 'checked_in' || $checkin->status === 'present' ? 'badge-success bg-emerald-500/10 border-emerald-500/25 text-emerald-400' : 'badge-warning bg-amber-500/10 border-amber-500/25 text-amber-400' }}">
                                        {{ $checkin->status === 'checked_in' || $checkin->status === 'present' ? 'Presente' : 'Ausente' }}
                                    </span>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="text-center py-6 text-zinc-500">No se han registrado asistencias recientes</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

    </div>
</div>
