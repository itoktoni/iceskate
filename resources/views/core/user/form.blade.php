<x-layout>
    <x-form :model="$model">
        <x-card>
            <x-action form="form" />

                @bind($model)
                    <x-form-input col="6" name="name" />
                    <x-form-input col="3" name="username" />
                    <x-form-input col="3" name="phone" />
                    <x-form-input col="3" name="email" />
                    <x-form-input col="3" type="date" name="birthday" />
                    <x-form-textarea col="6" name="address"/>
                    <x-form-input col="3" name="password" type="password" />
                    <x-form-select col="3" class="search" name="role" :options="$roles" />
                    <x-form-select col="3" class="search" name="member" :options="$member" />
                    <x-form-select col="3" name="category" :options="$category" />
                @endbind

        </x-card>
    </x-form>
</x-layout>
