<x-layout>

    <x-card class="table-container">

        <div class="col-md-12">

            <x-form method="GET" x-init="" x-target="table" role="search" aria-label="Contacts"
                autocomplete="off" action="{{ moduleRoute('getTable') }}">
                <x-filter toggle="Filter" :fields="$fields" />
            </x-form>

            <x-form method="POST" :upload="true" action="{{ moduleRoute('getTable') }}">

                <x-action>
                    <input type="file" name="file" accept=".xls,.xlsx" class="btn btn-primary btn-sm pb-2">
                    <x-button type="submit" label="Upload" class="btn-dark" name="upload" />
                </x-action>

                <div class="container-fluid" id="table">
                    <div class="table-responsive">
                        <table class="table table-bordered table-striped">
                            <thead>
                                <tr>
                                    <th width="9" style="width: 10px" class="center">
                                        <input class="btn-check-d" type="checkbox">
                                    </th>
                                    <th class="text-center">{{ __('Action') }}</th>
                                    <th>ID</th>
                                    <th>Nama</th>
                                    <th>Tanggal</th>
                                    <th>Keterangan</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($data as $table)
                                    <tr>
                                        <td>
                                            <input type="checkbox" class="checkbox" name="code[]"
                                                value="{{ $table->field_primary }}">
                                        </td>
                                        <td class=" text-center">
                                            <x-crud :action="['delete']" :model="$table">
                                                <a class="btn btn-primary copy-link" data-link="{{ route('kehadiran', ['id' => $table->field_primary]) }}" href="javascript:void(0)" onclick="copyLink(this)">Copy Link</a>
                                                <x-button module="getUpdate" key="{{ $table->field_primary }}" color="secondary" label="Kehadiran"/>
                                                <x-button module="getRace" key="{{ $table->field_primary }}" color="success" label="Performance"/>
                                            </x-crud>
                                        </td>

										<td style="width: 50px">{{ $table->jadwal_id }}</td>
										<td style="width: 250px">{{ $table->jadwal_nama }}</td>
										<td style="width: 120px">{{ $table->jadwal_tanggal }}</td>
										<td>
                                            {{ $table->jadwal_keterangan }}
                                            <br>
                                            <a href="{{ $table->jadwal_url }}" target="_blank">{{ $table->jadwal_url }}</a>
                                            <br>
                                            <a href="{{ $table->jadwal_link }}" target="_blank">{{ $table->jadwal_link }}</a>
                                        </td>

                                    </tr>
                                @empty
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    <x-pagination :data="$data" />
                </div>

            </x-form>

            <script>
                function copyLink(button) {
                    // Get the value of the 'data-link' attribute using the .dataset property
                    const linkToCopy = button.dataset.link;

                    // Try the Clipboard API first
                    if (navigator.clipboard && navigator.clipboard.writeText) {
                        navigator.clipboard.writeText(linkToCopy)
                            .then(() => {
                                // Provide user feedback
                                console.log('Link copied to clipboard:', linkToCopy);
                                button.textContent = 'Copied!';
                                setTimeout(() => {
                                    button.textContent = 'Copy Link';
                                }, 2000);
                            })
                            .catch(err => {
                                // Fallback to legacy method
                                fallbackCopyText(linkToCopy, button);
                            });
                    } else {
                        // Fallback to legacy method
                        fallbackCopyText(linkToCopy, button);
                    }
                }

                function fallbackCopyText(text, button) {
                    // Create a temporary textarea element
                    const textArea = document.createElement("textarea");
                    textArea.value = text;

                    // Ensure the textarea is not visible but still in the DOM
                    textArea.style.position = "fixed";
                    textArea.style.left = "-9999px";
                    textArea.style.top = "0";
                    document.body.appendChild(textArea);

                    // Focus and select the text
                    textArea.focus();
                    textArea.select();

                    try {
                        // Execute the copy command
                        const successful = document.execCommand('copy');
                        if (successful) {
                            console.log('Link copied to clipboard (fallback):', text);
                            button.textContent = 'Copied!';
                            setTimeout(() => {
                                button.textContent = 'Copy Link';
                            }, 2000);
                        } else {
                            alert('Failed to copy the link. You can copy it manually.');
                        }
                    } catch (err) {
                        console.error('Failed to copy link (fallback): ', err);
                        alert('Failed to copy the link. You can copy it manually.');
                    }

                    // Clean up
                    document.body.removeChild(textArea);
                }
            </script>

        </div>

    </x-card>

</x-layout>
