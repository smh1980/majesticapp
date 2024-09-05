{{-- <!DOCTYPE html> --}}
{{-- <html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">

        <title>{{ $title ?? 'Majestic App.' }}</title>
        @vite(['resources/css/app.css', 'resources/css/custom.css', 'resources/js/app.js', ])
        @stack('styles')
        @livewireStyles
    </head>
    <body class="bg-[#F5E8EF] dark:bg-slate-700 h-full overflow-x-hidden">
        @livewire('partials.navbar')
        <main>
            {{ $slot }}
        </main>
        @livewire('partials.footer')
        @livewireScripts
        <script src="//cdn.jsdelivr.net/npm/sweetalert2@11"></script>
        <x-livewire-alert::scripts />
        <script src="https://cdn.jsdelivr.net/gh/livewire/turbolinks@v0.1.x/dist/livewire-turbolinks.js" data-turbolinks-eval="false" data-turbo-eval="false"></script>
    </body>
</html> --}}

<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">

        <title>{{ $title ?? 'Majestic App.' }}</title>
        @vite(['resources/css/app.css', 'resources/css/custom.css', 'resources/js/app.js'])
        @stack('styles')
        @livewireStyles
    </head>
    <body class="bg-[#F5E8EF] dark:bg-slate-700 h-full overflow-x-hidden">
        @livewire('partials.navbar')
        <main>
            {{ $slot }}
        </main>
        @livewire('partials.footer')
        
        
        
        <script src="//cdn.jsdelivr.net/npm/sweetalert2@11"></script>
        <x-livewire-alert::scripts />
        
        <script>
            document.addEventListener('livewire:initialized', () => {
                Livewire.on('fileDownload', (data) => {
                    const { content, contentType, fileName } = data[0];
                    const blob = new Blob([atob(content)], { type: contentType });
                    const url = window.URL.createObjectURL(blob);
                    const a = document.createElement('a');
                    a.href = url;
                    a.download = fileName;
                    a.click();
                    window.URL.revokeObjectURL(url);
                });
                Livewire.on('viewPdf', (data) => {
            console.log('viewPdf event received', data);
            const { content, contentType, fileName } = data[0];
            
            // Convert base64 to binary
            const binaryString = window.atob(content);
            const len = binaryString.length;
            const bytes = new Uint8Array(len);
            for (let i = 0; i < len; i++) {
                bytes[i] = binaryString.charCodeAt(i);
            }
            
            // Create Blob and URL
            const blob = new Blob([bytes], { type: contentType });
            const url = window.URL.createObjectURL(blob);
            
            // Create a container for the PDF viewer and download button
            const container = document.createElement('div');
            container.style.position = 'fixed';
            container.style.top = '0';
            container.style.left = '0';
            container.style.width = '100%';
            container.style.height = '100%';
            container.style.backgroundColor = 'rgba(0,0,0,0.8)';
            container.style.zIndex = '9999';
            
            // Create PDF viewer
            const viewer = document.createElement('iframe');
            viewer.src = url;
            viewer.style.width = '80%';
            viewer.style.height = '80%';
            viewer.style.position = 'absolute';
            viewer.style.top = '10%';
            viewer.style.left = '10%';
            viewer.style.border = 'none';
            
            // Create download button
            const downloadBtn = document.createElement('button');
            downloadBtn.textContent = 'Download PDF';
            downloadBtn.style.position = 'absolute';
            downloadBtn.style.bottom = '5%';
            downloadBtn.style.left = '50%';
            downloadBtn.style.transform = 'translateX(-50%)';
            downloadBtn.style.padding = '10px 20px';
            downloadBtn.style.backgroundColor = '#4CAF50';
            downloadBtn.style.color = 'white';
            downloadBtn.style.border = 'none';
            downloadBtn.style.cursor = 'pointer';
            
            downloadBtn.onclick = () => {
                const a = document.createElement('a');
                a.href = url;
                a.download = fileName;
                a.click();
            };
            
            // Create close button
            const closeBtn = document.createElement('button');
            closeBtn.textContent = 'X';
            closeBtn.style.position = 'absolute';
            closeBtn.style.top = '5%';
            closeBtn.style.right = '5%';
            closeBtn.style.padding = '5px 10px';
            closeBtn.style.backgroundColor = 'red';
            closeBtn.style.color = 'white';
            closeBtn.style.border = 'none';
            closeBtn.style.cursor = 'pointer';
            
            closeBtn.onclick = () => {
                document.body.removeChild(container);
                window.URL.revokeObjectURL(url);
            };
            
            // Append elements to container and add to body
            container.appendChild(viewer);
            container.appendChild(downloadBtn);
            container.appendChild(closeBtn);
            document.body.appendChild(container);
                });
            });
        </script>
        @livewireScripts
        @stack('scripts')
    </body>
</html>
