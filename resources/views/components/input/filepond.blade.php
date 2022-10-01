<div wire:ignore>
    @push('scripts')
        <script>
            let filePond = '';

            window.addEventListener('{{$initializeListener}}', fileDetails => {
                filePond = FilePond.create(document.querySelector('.filepond'),
                    {
                        files:
                        fileDetails.detail,
                        server: {
                            process: {
                                url: '{{route('admin.product-image.upload')}}',
                                method: 'POST',
                                headers: {
                                    'X-CSRF-TOKEN': '{{csrf_token()}}',
                                },
                                onload: (response) => {
                                    @this.
                                    set('file', response);
                                    return response
                                },
                            },
                            revert: {
                                url: '{{route('admin.product-image.revert')}}',
                                method: 'POST',
                                headers: {
                                    'X-CSRF-TOKEN': '{{csrf_token()}}',
                                },
                                onload: (response) => {
                                    @this.
                                    set('file', '');
                                    return response;
                                },
                            },
                            load: {
                                url: '{{route('admin.product-image.load')}}/'
                            },
                            remove: (source, load, error) => {
                                $.ajax({
                                    url: '{{route('admin.product-image.remove')}}/' + source,
                                    type: 'DELETE',
                                    headers: {
                                        'X-CSRF-TOKEN': '{{csrf_token()}}',
                                    },
                                    success: function () {
                                        load();
                                        @this.
                                        call('unsetImage');
                                    },
                                    error: function () {
                                        error();
                                    }
                                });
                            },
                        }
                    });
            });

            window.addEventListener('{{$destroyListener}}', () => {
                if (filePond !== '')
                    filePond.destroy();
            });

        </script>
    @endpush

    <input type="file" id="filepond" class="filepond" credits="false"/>
</div>
