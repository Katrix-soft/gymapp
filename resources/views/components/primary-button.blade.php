<button {{ $attributes->merge(['type' => 'submit', 'class' => 'inline-flex items-center justify-center px-6 py-3 border border-transparent rounded-xl font-semibold text-sm text-white uppercase tracking-wider brand-btn focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-offset-gray-950 transition ease-in-out duration-150']) }}>
    {{ $slot }}
</button>
