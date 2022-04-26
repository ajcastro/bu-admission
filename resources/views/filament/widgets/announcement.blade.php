<x-filament::card class="relative">
    <div class="relative h-12 flex flex-col justify-center items-center space-y-2 rtl:space-x-reverse">
        <div class="space-y-1">
            <br>
            <br>
            <br>
            <br>
            <h2 class="text-lg sm:text-xl font-bold tracking-tight">
                Announcement
            </h2>
        </div>
        <div class="space-y-1 text-gray-600 focus:outline-none focus:underline">
            <p>
                {{ \App\Models\Setting::value('announcement') ?? 'Nisi nulla ea aliquip ad reprehenderit consequat fugiat nulla sit non velit eiusmod minim.' }}
            </p>
            <hr>
            <p>
                <a href="{{ \App\Models\Setting::value('google_drive') ?? 'https://drive.google.com/'}}" target="_blank">
                    Google Drive Link:
                    {{ \App\Models\Setting::value('google_drive') ?? 'https://drive.google.com/'}}
                </a>
            </p>
        </div>
    </div>
</x-filament::card>
