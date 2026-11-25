<form method="POST" action="{{ route('locale.update') }}" class="language-switcher" style="display:flex;align-items:center;gap:6px;font-size:14px;flex-shrink:0">
    @csrf
    <input type="hidden" name="return_to" value="{{ request()->getRequestUri() }}">
    <label><span class="sr-only" style="position:absolute;width:1px;height:1px;overflow:hidden;clip:rect(0,0,0,0)">{{ __('Bahasa') }}</span>
        <select name="locale" aria-label="{{ __('Bahasa') }}" style="border:1px solid #98a2b3;border-radius:8px;padding:8px;background:transparent;color:inherit;max-width:130px">
            @foreach(['en' => 'English', 'id' => 'Indonesia', 'zh_CN' => '中文（简体）'] as $code => $name)
                <option value="{{ $code }}" @selected(app()->getLocale() === $code)>{{ $name }}</option>
            @endforeach
        </select>
    </label>
    <button type="submit" style="border:1px solid #98a2b3;border-radius:8px;padding:8px;cursor:pointer;white-space:nowrap">{{ __('Terapkan') }}</button>
</form>
