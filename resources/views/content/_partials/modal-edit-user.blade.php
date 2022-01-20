<!-- Edit User Modal -->
<div class="modal fade" id="editUser" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered modal-edit-user">
        <div class="modal-content">
            <div class="modal-header bg-transparent">
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body pb-5 px-sm-5 pt-50">
                <div class="text-center mb-2">
                    <h1 class="mb-1">تعديل بيانات المستخدم</h1>
                </div>
                <form id="editUserForm" class="row gy-1 pt-75" method="POST" action="{{route('admin.user.update')}}">
                    @csrf
                    <input type="hidden" id="object-id" name="object_id" value="{{$object->id}}">
                    <div class="col-12 col-md-6">
                        <label class="form-label" for="name">الاسم</label>
                        <input type="text" id="name" name="name" class="form-control"
                               placeholder="الاسم" value="{{$object->name}}"/>
                    </div>
                    <div class="col-12 col-md-6">
                        <label class="form-label" for="email">البريد الإلكتروني</label>
                        <input
                            type="email" id="email" name="email" class="form-control"
                            value="{{$object->email}}" placeholder="البريد الإلكتروني"/>
                    </div>
                    <div class="col-12 col-md-6">
                        <label class="form-label" for="phone">رقم الموبايل</label>
                        <input
                            type="text" id="phone" name="phone" class="form-control modal-edit-tax-id"
                            placeholder="رقم الموبايل" value="{{$object->gsm}}"/>
                    </div>
                    <div class="col-12 text-center mt-2 pt-50">
                        <button type="submit" class="btn btn-primary me-1">إرسال</button>
                        <button type="reset" class="btn btn-outline-secondary" data-bs-dismiss="modal"
                                aria-label="Close">
                            تراجع
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
<!--/ Edit User Modal -->
