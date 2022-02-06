@if (config('filament.layout.footer.should_show_logo'))
    <div class="flex items-center justify-center text-gray-300">
        {{ config('app.name') }}
    </div>
@endif
