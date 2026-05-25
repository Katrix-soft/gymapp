@props(['disabled' => false])

<input @disabled($disabled) {{ $attributes->merge(['class' => 'bg-gray-900/50 border border-gray-800 text-gray-100 rounded-xl px-4 py-2.5 shadow-sm focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-offset-gray-950 brand-focus transition-all duration-200']) }}>
