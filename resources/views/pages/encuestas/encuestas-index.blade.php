@extends('layouts.app')

@section('content')
    <div class="space-y-6">

    @php
        $orders = [
            [
                'product' => 'TailGrids',
                'category' => 'UI Kit',
                'countryFlag' => '/images/country/country-01.svg',
                'country' => 'USA',
                'cr' => 'Dashboard',
                'value' => '$12,499',
            ],
            [
                'product' => 'GrayGrids',
                'category' => 'Templates',
                'countryFlag' => '/images/country/country-03.svg',
                'country' => 'UK',
                'cr' => 'Dashboard',
                'value' => '$5,498',
            ],
            [
                'product' => 'Uideck',
                'category' => 'Templates',
                'countryFlag' => '/images/country/country-04.svg',
                'country' => 'Canada',
                'cr' => 'Dashboard',
                'value' => '$4,521',
            ],
            [
                'product' => 'FormBold',
                'category' => 'SaaS',
                'countryFlag' => '/images/country/country-05.svg',
                'country' => 'Australia',
                'cr' => 'Dashboard',
                'value' => '$13,843',
            ],
            [
                'product' => 'NextAdmin',
                'category' => 'Dashboard',
                'countryFlag' => '/images/country/country-06.svg',
                'country' => 'Germany',
                'cr' => 'Dashboard',
                'value' => '$7,523',
            ],
            [
                'product' => 'Form Builder',
                'category' => 'SaaS',
                'countryFlag' => '/images/country/country-07.svg',
                'country' => 'France',
                'cr' => 'Dashboard',
                'value' => '$1,377',
            ],
            [
                'product' => 'AyroUI',
                'category' => 'UI Kit',
                'countryFlag' => '/images/country/country-08.svg',
                'country' => 'Japan',
                'cr' => 'Dashboard',
                'value' => '$599,00',
            ],
        ];
    @endphp

    <div
        class="overflow-hidden rounded-2xl border border-gray-200 bg-white pt-4 dark:border-gray-800 dark:bg-white/[0.03]"
    >
        <div class="flex flex-col gap-4 px-6 mb-4 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <h3 class="text-lg font-semibold text-gray-800 dark:text-white/90">Encuestas</h3>
            </div>

            <div class="flex items-center gap-3">
                <div class="hidden xl:block">
                    <form>
                        <div class="relative">
                            <span class="absolute -translate-y-1/2 pointer-events-none left-4 top-1/2">
                                <!-- Search Icon -->
                                <svg class="fill-gray-500 dark:fill-gray-400" width="20" height="20"
                                    viewBox="0 0 20 20" fill="none">
                                    <path fill-rule="evenodd" clip-rule="evenodd"
                                        d="M3.04175 9.37363C3.04175 5.87693 5.87711 3.04199 9.37508 3.04199C12.8731 3.04199 15.7084 5.87693 15.7084 9.37363C15.7084 12.8703 12.8731 15.7053 9.37508 15.7053C5.87711 15.7053 3.04175 12.8703 3.04175 9.37363ZM9.37508 1.54199C5.04902 1.54199 1.54175 5.04817 1.54175 9.37363C1.54175 13.6991 5.04902 17.2053 9.37508 17.2053C11.2674 17.2053 13.003 16.5344 14.357 15.4176L17.177 18.238C17.4699 18.5309 17.9448 18.5309 18.2377 18.238C18.5306 17.9451 18.5306 17.4703 18.2377 17.1774L15.418 14.3573C16.5365 13.0033 17.2084 11.2669 17.2084 9.37363C17.2084 5.04817 13.7011 1.54199 9.37508 1.54199Z"
                                        fill="" />
                                </svg>
                            </span>
                            <input type="text" placeholder="Busqueda"
                                class="dark:bg-dark-900 h-11 w-full rounded-lg border border-gray-200 bg-transparent py-2.5 pl-12 pr-14 text-sm text-gray-800 shadow-theme-xs placeholder:text-gray-400 focus:border-brand-300 focus:outline-hidden focus:ring-3 focus:ring-brand-500/10 dark:border-gray-800 dark:bg-white/3 dark:text-white/90 dark:placeholder:text-white/30 dark:focus:border-brand-800 xl:w-[430px]" />
                        </div>
                    </form>
                </div>
                <button
                    class="inline-flex items-center gap-2 rounded-lg border border-gray-300 bg-white px-4 py-3 text-theme-sm font-medium text-gray-700 shadow-theme-xs hover:bg-gray-50 hover:text-gray-800 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-400 dark:hover:bg-white/[0.03] dark:hover:text-gray-200"
                >
                    <svg
                        class="stroke-current fill-white dark:fill-gray-800"
                        width="20"
                        height="20"
                        viewBox="0 0 20 20"
                        fill="none"
                        xmlns="http://www.w3.org/2000/svg"
                    >
                        <path
                            d="M2.29004 5.90393H17.7067"
                            stroke=""
                            stroke-width="1.5"
                            stroke-linecap="round"
                            stroke-linejoin="round"
                        />
                        <path
                            d="M17.7075 14.0961H2.29085"
                            stroke=""
                            stroke-width="1.5"
                            stroke-linecap="round"
                            stroke-linejoin="round"
                        />
                        <path
                            d="M12.0826 3.33331C13.5024 3.33331 14.6534 4.48431 14.6534 5.90414C14.6534 7.32398 13.5024 8.47498 12.0826 8.47498C10.6627 8.47498 9.51172 7.32398 9.51172 5.90415C9.51172 4.48432 10.6627 3.33331 12.0826 3.33331Z"
                            fill=""
                            stroke=""
                            stroke-width="1.5"
                        />
                        <path
                            d="M7.91745 11.525C6.49762 11.525 5.34662 12.676 5.34662 14.0959C5.34661 15.5157 6.49762 16.6667 7.91745 16.6667C9.33728 16.6667 10.4883 15.5157 10.4883 14.0959C10.4883 12.676 9.33728 11.525 7.91745 11.525Z"
                            fill=""
                            stroke=""
                            stroke-width="1.5"
                        />
                    </svg>

                    Filtros
                </button>
                <a href="/encuestas/create">
                    <x-ui.button size="sm" variant="primary">Nueva encuesta</x-ui.button>
                </a>
            </div>
        </div>

        <div class="max-w-full overflow-x-auto custom-scrollbar">
            <table class="min-w-full">
                <!-- table header start -->
                <thead>
                    <tr class="border-gray-100 border-y dark:border-white/[0.05]">
                        <th class="px-6 py-3">
                            <div class="flex items-center col-span-2">
                                <p class="font-medium text-gray-500 text-theme-xs dark:text-gray-400">Código</p>
                            </div>
                        </th>
                        <th class="px-6 py-3">
                            <div class="flex items-center">
                                <p class="font-medium text-gray-500 text-theme-xs dark:text-gray-400">Encuesta</p>
                            </div>
                        </th>
                        <th class="px-6 py-3">
                            <div class="flex items-center col-span-2">
                                <p class="font-medium text-gray-500 text-theme-xs dark:text-gray-400">Estado</p>
                            </div>
                        </th>
                        <th class="px-6 py-3">
                            <div class="flex items-center col-span-2">
                                <p class="font-medium text-gray-500 text-theme-xs dark:text-gray-400">Acciones</p>
                            </div>
                        </th>
                    </tr>
                </thead>
                <!-- table header end -->

                <!-- table body start -->
                <tbody class="divide-y divide-gray-100 dark:divide-white/[0.05]">
                    @foreach ($orders as $order)
                        <tr>
                            <td class="px-6 py-3.5">
                                <div class="flex items-center">
                                    <p class="font-medium text-gray-800 text-theme-sm dark:text-white/90">
                                        {{ $order['product'] }}
                                    </p>
                                </div>
                            </td>
                            <td class="px-6 py-3.5">
                                <div class="flex items-center">
                                    <p class="text-gray-500 text-theme-sm dark:text-gray-400">{{ $order['category'] }}</p>
                                </div>
                            </td>
                            <td class="px-6 py-3.5">
                                <div class="flex items-center">
                                    <div class="w-5 h-5 overflow-hidden rounded-full">
                                        <img src="{{ $order['countryFlag'] }}" alt="{{ $order['country'] }}" />
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-3.5">
                                <div class="flex items-center">
                                    <p class="text-gray-500 text-theme-sm dark:text-gray-400">
                                        {{ $order['cr'] }}
                                    </p>
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
                <!-- table body end -->
            </table>
        </div>
    </div>

    </div>
@endsection
