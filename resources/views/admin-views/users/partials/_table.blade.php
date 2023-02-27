@foreach ($users as $user)
    <tr>
        <td>{{ $loop->index + 1 }}</td>
        <td>
            <div style="height: 100px; width: 100px; overflow-x: hidden;overflow-y: hidden">
                <img src="{{ asset('storage/app/public/users/' . $user->image) }}" style="width: 100px"
                    onerror="this.src='{{ asset('public/assets/admin/img/160x160/img1.jpg') }}'">
            </div>
        </td>
        <td>
            <span class="d-block font-size-sm text-body">
                {{ $user->f_name }}
            </span>
        </td>
        <td>
            <span class="d-block font-size-sm text-body">
                {{ $user->l_name }}
            </span>
        </td>
        <td>
            <span class="d-block font-size-sm text-body">
                {{ $user->email }}
            </span>
        </td>
        <td>
            <span class="d-block font-size-sm text-body">
                {{ $user->phone }}
            </span>
        </td>
        <td>
            <span class="d-block font-size-sm text-body">
                {{ implode(', ', $user->roles->pluck('name')->toArray()) }}
            </span>
        </td>
        <td>
            <!-- Dropdown -->
            @if (auth('admin')->user()->hasRole('super-admin'))
                <div class="dropdown">
                    <button class="btn btn-secondary dropdown-toggle" type="button" id="dropdownMenuButton"
                        data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                        <i class="tio-settings"></i>
                    </button>
                    <div class="dropdown-menu" aria-labelledby="dropdownMenuButton">
                        <a class="dropdown-item"
                            href="{{ route('admin.users.edit', [$user->id]) }}">{{ \App\CentralLogics\translate('edit') }}</a>
                        <a class="dropdown-item" href="javascript:"
                            onclick="form_alert('user-{{ $user->id }}','{{ \App\CentralLogics\translate('Want to delete this item ?') }}')">{{ \App\CentralLogics\translate('delete') }}</a>
                        <form action="{{ route('admin.users.delete', [$user->id]) }}" method="post"
                            id="user-{{ $user->id }}">
                            @csrf @method('delete')
                        </form>
                    </div>
                </div>
            @endif
            <!-- End Dropdown -->
        </td>
    </tr>
@endforeach
