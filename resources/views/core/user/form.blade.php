<x-layout>
    <x-form :model="$model">
        <x-card>
            <x-action form="form" />

                @bind($model)
                    <x-form-input col="6" name="name" />
                    <x-form-input col="6" name="username" />
                    <x-form-input col="6" name="phone" />
                    <x-form-input col="6" name="email" />
                    <x-form-select col="3" class="search" name="role" :options="$roles" />
                    <x-form-select col="3" name="category" :options="$category" />
                    <x-form-input col="6" name="password" type="password" />
                @endbind

        </x-card>
    </x-form>
</x-layout>
