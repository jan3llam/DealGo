<!-- Add Role Modal -->
<div class="modal fade" id="addRoleModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered modal-add-new-role">
        <div class="modal-content">
            <div class="modal-header bg-transparent">
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body px-5 pb-5">
                <div class="text-center mb-4">
                    <h1 class="role-title">{{__('locale.AddNew')}} {{__('locale.Role')}}</h1>
                    <p>{{__('locale.SetRolePermissions')}}</p>
                </div>
                <!-- Add role form -->
                <form id="addRoleForm" class="row" action="{{route('admin.roles.add')}}" method="post">
                    @csrf
                    <div class="col-12">
                        <label class="form-label" for="modalRoleName">{{__('locale.Name')}}</label>
                        <input type="text" id="modalRoleName" name="name" class="form-control"
                               placeholder="{{__('locale.Name')}}" tabindex="-1" data-msg="{{__('locale.FillField')}}"/>
                    </div>
                    <div class="col-12">
                        <label class="form-label" for="modalRoleName">{{__('locale.Description')}}</label>
                        <input type="text" id="modalRoleName" name="description" class="form-control"
                               placeholder="{{__('locale.Description')}}" tabindex="-1"
                               data-msg="{{__('locale.FillField')}}"/>
                    </div>
                    <div class="col-12">
                        <h4 class="mt-2 pt-50">{{__('locale.Role')}} {{__('locale.Permissions')}}</h4>
                        <!-- Permission table -->
                        <div class="table-responsive">
                            <table class="table table-flush-spacing">
                                <tbody>
                                <tr>
                                    <td class="text-nowrap fw-bolder">
                                        {{__('locale.AdministratorAccess')}}
                                        <span data-bs-toggle="tooltip" data-bs-placement="top"
                                              title="{{__('locale.AdministratorAccessMsg')}}">
                                                <i data-feather="info"></i>
                                        </span>
                                    </td>
                                    <td>
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" name="all" id="selectAll"/>
                                            <label class="form-check-label"
                                                   for="selectAll">{{__('locale.SelectAll')}}</label>
                                        </div>
                                    </td>
                                </tr>
                                @foreach($permissions->groupBy('group') as $group)
                                    <tr>
                                        <td class="text-nowrap fw-bolder">{{$group->first()->group}}</td>
                                        <td>
                                            <div class="d-flex">
                                                @foreach($group as $permission)
                                                    <div class="form-check me-3 me-lg-5">
                                                        <input class="form-check-input" type="checkbox"
                                                               id="permission-{{$permission->id}}"
                                                               name="permissions[][{{$permission->id}}]"/>
                                                        <label class="form-check-label"
                                                               for="permission-{{$permission->id}}">
                                                            {{$permission->name}}
                                                        </label>
                                                    </div>
                                                @endforeach
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                                </tbody>
                            </table>
                        </div>
                        <span style="display: none" id="permissions-msg"></span>
                        <!-- Permission table -->
                    </div>
                    <div class="col-12 text-center mt-2">
                        <button type="submit" class="btn btn-primary me-1">
                            {{__('locale.Submit')}}
                        </button>
                        <button type="reset" class="btn btn-outline-secondary" data-bs-dismiss="modal"
                                aria-label="Close">
                            {{__('locale.Cancel')}}
                        </button>
                    </div>
                </form>
                <!--/ Add role form -->
            </div>
        </div>
    </div>
</div>
<!--/ Add Role Modal -->
