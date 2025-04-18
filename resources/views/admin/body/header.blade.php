<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

  <header id="page-topbar">
      <div class="navbar-header">
          <div class="d-flex">
              <!-- LOGO -->
              <div class="navbar-brand-box">
                  <a href="{{ route('admin.dashboard') }}" class="logo logo-dark">
                      <span class="logo-sm">
                          <h2>RUPA</h2>
                          {{-- <img src="{{ asset('backend/assets/images/logo-sm.png') }}" alt="logo-sm" height="22"> --}}
                      </span>
                      <span class="logo-lg">
                          {{-- <img src="{{ asset('backend/assets/images/logo-dark.png') }}" alt="logo-dark" height="20"> --}}
                          <h2>RUPA</h2>
                      </span>
                  </a>

                  <a href="{{ route('admin.dashboard') }}" class="logo logo-light">
                      <span class="logo-sm">
                          {{-- <img src="{{ asset('backend/assets/images/logo-sm.png') }}" alt="logo-sm-light"
                              height="22"> --}}
                          <h2 class="text-light mt-3">RUPA</h2>

                      </span>
                      <span class="logo-lg">
                          {{-- <img src="{{ asset('backend/assets/images/logo-light.png') }}" alt="logo-light" height="20"> --}}
                          <h2 class="text-light mt-3">RUPA</h2>

                      </span>
                  </a>
              </div>

              <button type="button" class="btn btn-sm px-3 font-size-24 header-item waves-effect"
                  id="vertical-menu-btn">
                  <i class="ri-menu-2-line align-middle"></i>
              </button>

              <!-- App Search-->
              <form class="app-search d-none d-lg-block">
                  <div class="position-relative">
                      <input type="text" class="form-control" placeholder="Search...">
                      <span class="ri-search-line"></span>
                  </div>
              </form>


          </div>

          <div class="d-flex align-items-center">

              @if (Route::current()->getName() == 'invoice.add')
                  <div style="margin-left: 10px;">
                      <a href="{{ route('add.salary') }}">
                          <i class="btn btn-info btn wave-effect wave-light fas fa-plus-circle">
                              Add Salary</i>
                      </a>
                  </div>
                  <div style="margin-left: 10px;">
                        <a href="{{ route('invoice.add.local') }}">
                          <i class="btn btn-info btn wave-effect wave-light fas fa-plus-circle">
                              Local Bill</i>
                        </a>
                  </div>
                  
                  <div class="dropdown d-none d-lg-inline-block ms-1">
                      <button type="button" class="btn header-item noti-icon waves-effect" data-toggle="fullscreen">
                          <i class="ri-fullscreen-line"></i>
                      </button>
                  </div>
              @elseif(Route::current()->getName() == 'invoice.add.local')
                  <div>
                      <a href="{{ route('add.salary') }}">
                          <i class="btn btn-info btn wave-effect wave-light fas fa-plus-circle">
                              Add Salary</i>
                      </a>
                  </div>
                  <div style="margin-left: 10px;">
                      <a href="{{ route('invoice.add') }}">
                          <i class="btn btn-info btn wave-effect wave-light fas fa-plus-circle">
                               Corporate Bill</i>
                      </a>
                  </div>
                  <div class="dropdown d-none d-lg-inline-block ms-1">
                      <button type="button" class="btn header-item noti-icon waves-effect" data-toggle="fullscreen">
                          <i class="ri-fullscreen-line"></i>
                      </button>
                  </div>

              @else
                  <div>
                      <a href="{{ route('add.salary') }}">
                          <i class="btn btn-info btn wave-effect wave-light fas fa-plus-circle">
                              Add Salary</i>
                      </a>
                  </div>
                  <div style="margin-left: 10px;">
                      <a href="{{ route('invoice.add') }}">
                          <i class="btn btn-info btn wave-effect wave-light fas fa-plus-circle">
                              Corporate Bill</i>
                      </a>
                  </div>
                  <div style="margin-left: 10px;">
                      <a href="{{ route('invoice.add.local') }}">
                          <i class="btn btn-info btn wave-effect wave-light fas fa-plus-circle">
                              Local Bill</i>
                      </a>
                  </div>
                  
              @endif

              @if (Request::routeIs('add.purchase') == false)
                <div>
                    <a href="{{ route('add.purchase') }}" style="margin-left: 10px;">
                        <i class="btn btn-info btn wave-effect wave-light fas fa-plus-circle">
                            Add Purchase</i>
                    </a>
                </div>
              @endif

            <div class="dropdown d-none d-lg-inline-block ms-1">
                <button type="button" class="btn header-item noti-icon waves-effect" data-toggle="fullscreen">
                    <i class="ri-fullscreen-line"></i>
                </button>
            </div>

              @php
                  $id = Auth::user()->id;
                  $adminData = App\Models\User::find($id);
              @endphp

              <div class="dropdown d-inline-block user-dropdown">
                  <button type="button" class="btn header-item waves-effect" id="page-header-user-dropdown"
                      data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                      <img class="rounded-circle header-profile-user"
                          src="{{ !empty($adminData->photo) ? url('upload/admin_images/' . $adminData->photo) : url('upload/no_image.jpg') }}"
                          alt="Header Avatar">
                      <span class="d-none d-xl-inline-block ms-1">{{ Auth::user()->name }}</span>
                      <i class="mdi mdi-chevron-down d-none d-xl-inline-block"></i>
                  </button>
                  <div class="dropdown-menu dropdown-menu-end">
                      <!-- item-->
                      <a class="dropdown-item" href="{{ route('admin.profile') }}"><i
                              class="ri-user-line align-middle me-1"></i> Profile</a>
                      <a class="dropdown-item d-block" href="{{ route('change.admin.password') }}"><i
                              class="ri-settings-2-line align-middle me-1"></i> Change Password</a>
                      <div class="dropdown-divider"></div>
                      <a class="dropdown-item text-danger" href="{{ route('admin.logout') }}"><i
                              class="ri-shut-down-line align-middle me-1 text-danger"></i> Logout</a>
                  </div>
              </div>


          </div>
      </div>
  </header>
