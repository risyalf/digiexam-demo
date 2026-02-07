<div class="p-4">
    @php
        $options = is_string($record->options) ? json_decode($record->options, true) : $record->options;
    @endphp

    @if($options && is_array($options))
        <div class="space-y-3">
            @foreach($options as $option)
                @php
                    $isCorrect = ($option['value'] === 'true' || $option['value'] === true);
                @endphp
                <div class="flex items-start gap-3 p-3 rounded-lg border {{ $isCorrect ? 'border-success-500 bg-success-50/50' : 'border-gray-200' }}">
                    <div class="flex-1">
                        <p class="text-sm font-medium text-gray-900">
                            {{ $option['text'] }}
                        </p>
                    </div>
                    <div>
                        @if($isCorrect)
                            <span class="inline-flex items-center px-2 py-1 text-xs font-bold text-success-700 bg-success-100 rounded">
                                BENAR
                            </span>
                        @else
                            <span class="inline-flex items-center px-2 py-1 text-xs font-bold text-danger-700 bg-danger-100 rounded">
                                SALAH
                            </span>
                        @endif
                    </div>
                </div>
            @endforeach
        </div>
    @else
        <p class="text-gray-500 italic text-center">Tidak ada pilihan jawaban tersedia.</p>
    @endif
</div>