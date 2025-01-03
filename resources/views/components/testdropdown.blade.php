@foreach ($dropdown_list as $moduleName => $module)
    @php
        $id = strtolower(Str::slug($moduleName, '_'));
        $moduleKey = Str::slug(strtolower(trim(explode('Select', $moduleName)[1])) . "_id", '_');
        $selectedValue = request()->input($moduleKey);
    @endphp
    <div>
        <select id="{{ $id }}" name="{{ $moduleKey }}" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500 required-field">
            <option value="">{{$moduleName}}</option>
            @foreach($module as $item)
                <option value="{{$item->id}}" {{ $selectedValue == $item->id ? 'selected' : '' }}>{{$item->name}}</option>
            @endforeach
        </select>
        <div class="text-red-500 text-xs mt-1 validation-msg"></div> 
        @error('module.' . $moduleKey)
            <div class="text-red-500 text-xs mt-1">{{ $message }}</div>
        @enderror
    </div>
@endforeach
