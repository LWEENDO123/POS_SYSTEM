- [x] Read Routes/AuthController/Dashboard view and identify why 404 happens.
- [x] Add dashboard route to app/Config/Routes.php with comments.
- [x] Create app/Controllers/DashBoard.php with index() returning view('dashboard/index').
- [x] Fix AuthController view paths: view('/auth/login') -> view('auth/login').
- [ ] Fix remaining 404: Controller or method not found: App\Controllers\CreateAccount::user_page.
- [ ] Verify where CreateAccount class actually lives (likely inside AuthController.php) and ensure routing can find it.
- [ ] Re-test routes list and the failing URL(s).

