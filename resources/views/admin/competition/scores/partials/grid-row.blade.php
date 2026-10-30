@php
    $currentVal = isset($existingRubric[$key]) ? (int)$existingRubric[$key] : 0;
    $maxVal = end($options);
@endphp
<tr id="row_{{ $key }}" class="hover:bg-slate-50/80 transition" data-key="{{ $key }}" data-section="{{ $section }}" data-max="{{ $maxVal }}">
    <td class="py-3 px-4 text-center text-slate-400 font-bold font-mono">{{ $letter }}</td>
    <td class="py-3 px-5 font-bold text-slate-800 text-xs leading-relaxed">
        {{ $label }}
    </td>
    <td class="py-3 px-4 text-center">
        <!-- Hidden input for form submission -->
        <input type="hidden" name="rubric[{{ $key }}]" id="input_{{ $key }}" class="rubric-input" data-section="{{ $section }}" value="{{ $currentVal }}">

        <!-- Buttons: K (0), C (mid), B (max) -->
        <div class="inline-flex items-center justify-center gap-1.5 p-1 bg-slate-100/70 rounded-xl border border-slate-200">
            <!-- Kurang (0) -->
            <button type="button" onclick="selectOption('{{ $key }}', {{ $options[0] }})" data-type="k" data-value="{{ $options[0] }}" class="btn-score w-12 py-1.5 rounded-lg border border-slate-300 text-xs font-bold hover:bg-slate-200 transition {{ $currentVal === $options[0] ? 'active-k' : 'bg-white text-slate-600' }}" title="Kurang (K) - Nilai: {{ $options[0] }}">
                {{ $options[0] }}
            </button>
            
            <!-- Cukup (options[1]) -->
            <button type="button" onclick="selectOption('{{ $key }}', {{ $options[1] }})" data-type="c" data-value="{{ $options[1] }}" class="btn-score w-12 py-1.5 rounded-lg border border-amber-300 text-xs font-bold hover:bg-amber-100 transition {{ $currentVal === $options[1] ? 'active-c' : 'bg-white text-amber-700' }}" title="Cukup (C) - Nilai: {{ $options[1] }}">
                {{ $options[1] }}
            </button>

            <!-- Baik (options[2]) -->
            <button type="button" onclick="selectOption('{{ $key }}', {{ $options[2] }})" data-type="b" data-value="{{ $options[2] }}" class="btn-score w-12 py-1.5 rounded-lg border border-emerald-300 text-xs font-bold hover:bg-emerald-100 transition {{ $currentVal === $options[2] ? 'active-b' : 'bg-white text-emerald-700' }}" title="Baik (B) - Nilai: {{ $options[2] }}">
                {{ $options[2] }}
            </button>
        </div>
    </td>
    <td class="py-3 px-4 text-center bg-slate-50/50">
        <span class="row-score-display font-mono font-black text-sm {{ $currentVal === 0 ? 'text-slate-400' : ($currentVal === $maxVal ? 'text-emerald-700' : 'text-amber-700') }}">
            {{ $currentVal }}
        </span>
    </td>
</tr>
