{{--
  Phase 6 – Product listing view

  WHY: Blade templates receive data from the controller (here: $products) and
  output HTML. We use the same layout as the rest of the app (x-app-layout)
  so nav and styles are consistent. @foreach loops over the collection;
  {{ }} escapes output to prevent XSS. "Add to cart" posts to cart.store
  (Phase 7); if the user is not logged in, that route will redirect to login.
--}}
<x-app-layout>
    <x-slot name="header">
        <h1 class="text-2xl font-bold text-slate-900 tracking-tight">
            {{ __('Products') }}
        </h1>
    </x-slot>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        @if(session('status'))
            <div class="mb-8 rounded-xl border border-emerald-200 bg-emerald-50 p-4 shadow-sm" role="alert">
                <p class="mb-3 font-medium text-emerald-900">{{ session('status') }}</p>
                <div class="flex flex-wrap gap-3">
                    <a href="{{ url('/cart') }}" class="inline-flex items-center px-4 py-2.5 bg-slate-700 text-sm font-semibold rounded-lg hover:bg-slate-800 transition !text-white">
                        {{ __('View cart') }}
                    </a>
                    <a href="{{ url('/checkout') }}" class="inline-flex items-center px-4 py-2.5 bg-slate-900 text-sm font-semibold rounded-lg hover:bg-slate-800 transition !text-white">
                        {{ __('Checkout') }}
                    </a>
                </div>
            </div>
        @endif
        <div class="grid gap-6 sm:grid-cols-2 lg:grid-cols-3">
            @forelse($products as $product)
                <div class="bg-white rounded-xl border border-slate-200/80 shadow-sm overflow-hidden hover:shadow-md transition">
                    <div class="p-6">
                        <h2 class="font-semibold text-lg text-slate-900">{{ $product->title }}</h2>
                        <p class="mt-2 text-sm text-slate-600 line-clamp-3">
                            {{ $product->description }}
                        </p>
                        <p class="mt-4 text-xl font-bold text-slate-900">
                            R{{ number_format($product->price, 2) }}
                        </p>
                        @auth
                            <form method="POST" action="{{ route('cart.store') }}" class="mt-5">
                                @csrf
                                <input type="hidden" name="product_id" value="{{ $product->id }}">
                                <input type="hidden" name="quantity" value="1">
                                <x-primary-button type="submit">
                                    {{ __('Add to cart') }}
                                </x-primary-button>
                            </form>
                        @else
                            <p class="mt-5 text-sm text-slate-500">
                                <a href="{{ route('login') }}" class="font-medium text-slate-700 hover:text-slate-900 underline">{{ __('Log in') }}</a>
                                {{ __('to add to cart.') }}
                            </p>
                        @endauth
                    </div>
                </div>
            @empty
                <div class="col-span-full rounded-xl bg-white border border-slate-200/80 p-12 text-center text-slate-500">
                    {{ __('No products yet.') }}
                </div>
            @endforelse
        </div>
    </div>
</x-app-layout>
