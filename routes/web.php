<?php

use App\Http\Controllers\Admin\CustomFeatureRequestController as AdminCustomFeatureRequestController;
use App\Http\Controllers\Admin\DashboardController as AdminDashboardController;
use App\Http\Controllers\Admin\FinanceReportController;
use App\Http\Controllers\Admin\MembershipController as AdminMembershipController;
use App\Http\Controllers\Admin\OrderController as AdminOrderController;
use App\Http\Controllers\Admin\PromoCodeController as AdminPromoCodeController;
use App\Http\Controllers\Admin\ProductCategoryController as AdminProductCategoryController;
use App\Http\Controllers\Admin\ProductController as AdminProductController;
use App\Http\Controllers\Admin\ClassProductController as AdminClassProductController;
use App\Http\Controllers\Admin\TestimonialController as AdminTestimonialController;
use App\Http\Controllers\Admin\TransactionController as AdminTransactionController;
use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\Auth\EmailVerificationController;
use App\Http\Controllers\Auth\GoogleAuthController;
use App\Http\Controllers\Auth\NewPasswordController;
use App\Http\Controllers\Auth\PasswordResetLinkController;
use App\Http\Controllers\Auth\RegisteredUserController;
use App\Http\Controllers\Frontend\CartController;
use App\Http\Controllers\Frontend\CheckoutController;
use App\Http\Controllers\Frontend\HomeController;
use App\Http\Controllers\Frontend\ProductController;
use App\Http\Controllers\Member\DashboardController as MemberDashboardController;
use App\Http\Controllers\Member\ProfileController;
use App\Http\Controllers\Member\TestimonialController as MemberTestimonialController;
use App\Http\Controllers\Member\TransactionController as MemberTransactionController;
use App\Http\Controllers\Payment\PaymentWebhookController;
use App\Http\Controllers\Vendor\CustomFeatureRequestController as VendorCustomFeatureRequestController;
use App\Http\Controllers\Partner\DashboardController as PartnerDashboardController;
use App\Http\Controllers\Frontend\ProjectRequestController as PublicProjectRequestController;
use App\Http\Controllers\Member\ProjectRequestController as MemberProjectRequestController;
use Illuminate\Support\Facades\Route;

Route::get('/', HomeController::class)->name('home');
Route::get('/products', [ProductController::class, 'index'])->name('products.index');
Route::get('/products/{product:slug}', [ProductController::class, 'show'])->name('products.show');

use App\Http\Controllers\Frontend\ClassController;
Route::get('/kelas', [ClassController::class, 'index'])->name('kelas.index');
Route::get('/kelas/{product:slug}', [ClassController::class, 'show'])->name('kelas.show');

Route::get('/request', [PublicProjectRequestController::class, 'create'])->name('public.request.create');
Route::post('/request', [PublicProjectRequestController::class, 'store'])->name('public.request.store');
use App\Http\Controllers\Frontend\PartnerRegistrationController;
use App\Http\Controllers\Admin\PartnerSettingController as AdminPartnerSettingController;
use App\Http\Controllers\Member\PartnerStatusController as MemberPartnerStatusController;

Route::get('/partner/register', [PartnerRegistrationController::class, 'index'])->name('partner.register.index');

Route::middleware('guest')->group(function (): void {
    Route::get('/login', [AuthenticatedSessionController::class, 'create'])->name('login');
    Route::post('/login', [AuthenticatedSessionController::class, 'store']);
    Route::get('/register', [RegisteredUserController::class, 'create'])->name('register');
    Route::post('/register', [RegisteredUserController::class, 'store']);
    Route::get('/forgot-password', [PasswordResetLinkController::class, 'create'])->name('password.request');
    Route::post('/forgot-password', [PasswordResetLinkController::class, 'store'])->name('password.email');
    Route::get('/reset-password/{token}', [NewPasswordController::class, 'create'])->name('password.reset');
    Route::post('/reset-password', [NewPasswordController::class, 'store'])->name('password.store');
    Route::get('/auth/google', [GoogleAuthController::class, 'redirect'])->name('google.redirect');
    Route::get('/auth/google/callback', [GoogleAuthController::class, 'callback'])->name('google.callback');
});

Route::middleware('auth')->group(function (): void {
    Route::post('/logout', [AuthenticatedSessionController::class, 'destroy'])->name('logout');
    Route::get('/email/verify', [EmailVerificationController::class, 'notice'])->name('verification.notice');
    Route::get('/email/verify/{id}/{hash}', [EmailVerificationController::class, 'verify'])
        ->middleware(['signed', 'throttle:6,1'])
        ->name('verification.verify');
    Route::post('/email/verification-notification', [EmailVerificationController::class, 'resend'])
        ->middleware('throttle:6,1')
        ->name('verification.send');

});

Route::middleware(['auth', 'verified'])->group(function (): void {
    Route::get('/cart', [CartController::class, 'show'])->name('cart.show');
    Route::get('/cart/{product:slug}/buy-now', [CartController::class, 'buyNow'])->name('cart.buy-now');
    Route::post('/cart/{product:slug}', [CartController::class, 'store'])->name('cart.store');
    Route::patch('/cart/items/{item}', [CartController::class, 'update'])->name('cart.update');
    Route::post('/cart/clear-items', [CartController::class, 'clear'])->name('cart.clear');
    Route::post('/cart/items/{item}/destroy', [CartController::class, 'destroy'])->name('cart.destroy');

    Route::get('/checkout', [CheckoutController::class, 'show'])->name('checkout.show');
    Route::post('/checkout', [CheckoutController::class, 'store'])->name('checkout.store');


    Route::get('/partner/register/vendor', [PartnerRegistrationController::class, 'createVendor'])->name('partner.register.vendor');
    Route::post('/partner/register/vendor', [PartnerRegistrationController::class, 'storeVendor']);
    
    Route::get('/partner/register/mentor', [PartnerRegistrationController::class, 'createMentor'])->name('partner.register.mentor');
    Route::post('/partner/register/mentor', [PartnerRegistrationController::class, 'storeMentor']);

    Route::prefix('member')->name('member.')->group(function (): void {
        Route::get('/dashboard', MemberDashboardController::class)->name('dashboard');
        Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
        Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
        Route::get('/transactions/active', [MemberTransactionController::class, 'active'])->name('transactions.active');
        Route::get('/transactions/history', [MemberTransactionController::class, 'history'])->name('transactions.history');
        Route::get('/transactions/{transaction}/proof', [MemberTransactionController::class, 'viewProof'])->name('transactions.proof.show');
        Route::post('/transactions/{transaction}/proof', [MemberTransactionController::class, 'uploadProof'])->name('transactions.proof');
        Route::get('/downloads/{transactionItem}', [MemberTransactionController::class, 'download'])->name('downloads.show');
        Route::get('/transactions/{transaction}/testimonial', [MemberTestimonialController::class, 'create'])->name('testimonials.create');
        Route::post('/transactions/{transaction}/testimonial', [MemberTestimonialController::class, 'store'])->name('testimonials.store');
        
        
        Route::get('/partner-status', [MemberPartnerStatusController::class, 'index'])->name('partner-status');

        // Project Requests
        Route::get('/project-requests', [MemberProjectRequestController::class, 'index'])->name('project-requests.index');
        Route::get('/project-requests/create', [MemberProjectRequestController::class, 'create'])->name('project-requests.create');
        Route::post('/project-requests', [MemberProjectRequestController::class, 'store'])->name('project-requests.store');
        Route::get('/project-requests/{projectRequest}', [MemberProjectRequestController::class, 'show'])->name('project-requests.show');
        Route::get('/project-requests/{projectRequest}/edit', [MemberProjectRequestController::class, 'edit'])->name('project-requests.edit');
        Route::put('/project-requests/{projectRequest}', [MemberProjectRequestController::class, 'update'])->name('project-requests.update');
        Route::get('/project-requests/{projectRequest}/pay', [MemberProjectRequestController::class, 'pay'])->name('project-requests.pay');
        Route::post('/project-requests/{projectRequest}/pay', [MemberProjectRequestController::class, 'processPayment'])->name('project-requests.process-payment');
        Route::post('/project-requests/{projectRequest}/complete', [MemberProjectRequestController::class, 'complete'])->name('project-requests.complete');
        
        Route::get('/project-requests/{projectRequest}/complaint', [\App\Http\Controllers\Member\ProjectComplaintController::class, 'create'])->name('project-requests.complaint');
        Route::post('/project-requests/{projectRequest}/complaint', [\App\Http\Controllers\Member\ProjectComplaintController::class, 'store'])->name('project-requests.complaint.store');


        Route::get('/transactions/{transaction}/refund', [\App\Http\Controllers\Member\RefundRequestController::class, 'create'])->name('transactions.refund');
        Route::post('/transactions/{transaction}/refund', [\App\Http\Controllers\Member\RefundRequestController::class, 'store'])->name('transactions.refund.store');
    });
});

Route::prefix('admin')->name('admin.')->middleware(['auth', 'verified', 'role:admin'])->group(function (): void {
    Route::get('/dashboard', AdminDashboardController::class)->name('dashboard');
    Route::resource('categories', AdminProductCategoryController::class)->only(['index', 'store', 'update', 'destroy']);
    Route::resource('class-categories', \App\Http\Controllers\Admin\ClassCategoryController::class)->only(['index', 'store', 'update', 'destroy']);
    Route::resource('products', AdminProductController::class)->except(['show']);
    Route::resource('class-products', AdminClassProductController::class)->except(['show']);
    Route::get('/class-products/{product}/batches', [\App\Http\Controllers\Admin\ClassBatchController::class, 'index'])->name('class-products.batches.index');
    Route::post('/class-products/{product}/batches', [\App\Http\Controllers\Admin\ClassBatchController::class, 'store'])->name('class-products.batches.store');
    Route::patch('/class-products/{product}/batches', [\App\Http\Controllers\Admin\ClassBatchController::class, 'updateFromCollection'])->name('class-products.batches.update-collection');
    Route::delete('/class-products/{product}/batches', [\App\Http\Controllers\Admin\ClassBatchController::class, 'destroyFromCollection'])->name('class-products.batches.destroy-collection');
    Route::patch('/class-products/{product}/batches/{batch}', [\App\Http\Controllers\Admin\ClassBatchController::class, 'update'])->name('class-products.batches.update');
    Route::delete('/class-products/{product}/batches/{batch}', [\App\Http\Controllers\Admin\ClassBatchController::class, 'destroy'])->name('class-products.batches.destroy');
    Route::get('/class-participants', [\App\Http\Controllers\Admin\ClassParticipantController::class, 'index'])->name('class-participants.index');
    Route::get('/class-participants/{product}', [\App\Http\Controllers\Admin\ClassParticipantController::class, 'show'])->name('class-participants.show');
    
    Route::get('/product-approvals', [\App\Http\Controllers\Admin\ProductApprovalController::class, 'index'])->name('product-approvals.index');
    Route::patch('/product-approvals/{product}/approve', [\App\Http\Controllers\Admin\ProductApprovalController::class, 'approve'])->name('product-approvals.approve');
    Route::patch('/product-approvals/{product}/reject', [\App\Http\Controllers\Admin\ProductApprovalController::class, 'reject'])->name('product-approvals.reject');

    Route::get('/class-approvals', [\App\Http\Controllers\Admin\ClassApprovalController::class, 'index'])->name('class-approvals.index');
    Route::patch('/class-approvals/{product}/approve', [\App\Http\Controllers\Admin\ClassApprovalController::class, 'approve'])->name('class-approvals.approve');
    Route::patch('/class-approvals/{product}/reject', [\App\Http\Controllers\Admin\ClassApprovalController::class, 'reject'])->name('class-approvals.reject');

    Route::resource('promo-codes', AdminPromoCodeController::class)->only(['index', 'store', 'update', 'destroy']);
    Route::get('/orders', [AdminOrderController::class, 'index'])->name('orders.index');
    Route::get('/transactions', [AdminTransactionController::class, 'index'])->name('transactions.index');
    Route::post('/transactions/bulk-destroy', [AdminTransactionController::class, 'bulkDestroy'])->name('transactions.bulk-destroy');
    Route::post('/transactions/bulk-verify', [AdminTransactionController::class, 'bulkVerify'])->name('transactions.bulk-verify');
    Route::patch('/transactions/{transaction}/status', [AdminTransactionController::class, 'updateStatus'])->name('transactions.status');
    Route::post('/transactions/{transaction}/destroy', [AdminTransactionController::class, 'destroy'])->name('transactions.destroy');
    Route::patch('/transactions/{transaction}/verify', [AdminTransactionController::class, 'verifyPayment'])->name('transactions.verify');
    Route::resource('bank-accounts', \App\Http\Controllers\Admin\BankAccountController::class)->only(['index', 'store', 'update', 'destroy']);
    Route::get('/finance', [FinanceReportController::class, 'index'])->name('finance.index');
    Route::get('/testimonials', [AdminTestimonialController::class, 'index'])->name('testimonials.index');
    Route::patch('/testimonials/{testimonial}/approve', [AdminTestimonialController::class, 'approve'])->name('testimonials.approve');
    Route::patch('/testimonials/{testimonial}/reject', [AdminTestimonialController::class, 'reject'])->name('testimonials.reject');
    Route::resource('memberships', AdminMembershipController::class)->only(['index', 'store', 'update', 'destroy']);
    Route::delete('/memberships/user/{user}', [AdminMembershipController::class, 'destroyUser'])->name('memberships.destroy-user');
    Route::get('/custom-feature-requests', [AdminCustomFeatureRequestController::class, 'index'])->name('custom-feature-requests.index');
    Route::patch('/custom-feature-requests/{customFeatureRequest}/status', [AdminCustomFeatureRequestController::class, 'updateStatus'])->name('custom-feature-requests.status');
    Route::post('/custom-feature-requests/{customFeatureRequest}/invoice', [AdminCustomFeatureRequestController::class, 'createInvoice'])->name('custom-feature-requests.invoice');
    

    Route::get('/partner-settings', [AdminPartnerSettingController::class, 'index'])->name('partner-settings.index');
    Route::patch('/partner-settings', [AdminPartnerSettingController::class, 'update'])->name('partner-settings.update');

    // Route::get('/settings/payment', [\App\Http\Controllers\Admin\SettingController::class, 'payment'])->name('settings.payment');
    // Route::put('/settings/payment', [\App\Http\Controllers\Admin\SettingController::class, 'updatePayment'])->name('settings.payment.update');

    // Project Requests (Admin)
    Route::get('/project-requests', [\App\Http\Controllers\Admin\ProjectRequestController::class, 'index'])->name('project-requests.index');
    Route::get('/project-requests/{projectRequest}', [\App\Http\Controllers\Admin\ProjectRequestController::class, 'show'])->name('project-requests.show');
    Route::put('/project-requests/{projectRequest}/approve', [\App\Http\Controllers\Admin\ProjectRequestController::class, 'approve'])->name('project-requests.approve');
    Route::put('/project-requests/{projectRequest}/reject', [\App\Http\Controllers\Admin\ProjectRequestController::class, 'reject'])->name('project-requests.reject');
    Route::put('/project-requests/{projectRequest}/assign', [\App\Http\Controllers\Admin\ProjectRequestController::class, 'assignVendor'])->name('project-requests.assign');
    Route::delete('/project-requests/{projectRequest}', [\App\Http\Controllers\Admin\ProjectRequestController::class, 'destroy'])->name('project-requests.destroy');

    // Komplain Project
    Route::post('/project-complaints/{projectComplaint}/resolve', [\App\Http\Controllers\Admin\ProjectComplaintController::class, 'resolve'])->name('project-complaints.resolve');

    // Refund Requests
    Route::get('/refund-requests', [\App\Http\Controllers\Admin\RefundRequestController::class, 'index'])->name('refund-requests.index');
    Route::post('/refund-requests/{refundRequest}/process', [\App\Http\Controllers\Admin\RefundRequestController::class, 'process'])->name('refund-requests.process');

    Route::get('/partners', [\App\Http\Controllers\Admin\PartnerController::class, 'index'])->name('partners.index');
    Route::patch('/partners/{partnerProfile}/status', [\App\Http\Controllers\Admin\PartnerController::class, 'updateStatus'])->name('partners.status.update');
    Route::post('/partners/bulk-deactivate', [\App\Http\Controllers\Admin\PartnerController::class, 'bulkDeactivate'])->name('partners.bulk-deactivate');

    Route::get('/partner-withdrawals', [\App\Http\Controllers\Admin\PartnerWithdrawalController::class, 'index'])->name('partner-withdrawals.index');
    Route::post('/partner-withdrawals/{withdrawal}', [\App\Http\Controllers\Admin\PartnerWithdrawalController::class, 'update'])->name('partner-withdrawals.update');
});

Route::prefix('vendor')->name('vendor.')->middleware(['auth', 'verified', 'role:vendor'])->group(function (): void {
    Route::get('/custom-feature-requests', [VendorCustomFeatureRequestController::class, 'index'])->name('custom-feature-requests.index');
    Route::patch('/custom-feature-requests/{customFeatureRequest}/notes', [VendorCustomFeatureRequestController::class, 'updateNotes'])->name('custom-feature-requests.notes');
    Route::resource('products', \App\Http\Controllers\Vendor\ProductController::class)->except(['show']);
});

Route::prefix('mentor')->name('mentor.')->middleware(['auth', 'verified', 'role:mentor'])->group(function (): void {
    Route::resource('classes', \App\Http\Controllers\Mentor\ClassController::class)->except(['show']);
    Route::get('/classes/{product}/participants', [\App\Http\Controllers\Mentor\ClassParticipantController::class, 'index'])->name('classes.participants.index');
    
    Route::get('/classes/{product}/batches', [\App\Http\Controllers\Mentor\ClassBatchController::class, 'index'])->name('classes.batches.index');
    Route::post('/classes/{product}/batches', [\App\Http\Controllers\Mentor\ClassBatchController::class, 'store'])->name('classes.batches.store');
    Route::patch('/classes/{product}/batches', [\App\Http\Controllers\Mentor\ClassBatchController::class, 'updateFromCollection'])->name('classes.batches.update-collection');
    Route::delete('/classes/{product}/batches', [\App\Http\Controllers\Mentor\ClassBatchController::class, 'destroyFromCollection'])->name('classes.batches.destroy-collection');
    Route::patch('/classes/{product}/batches/{batch}', [\App\Http\Controllers\Mentor\ClassBatchController::class, 'update'])->name('classes.batches.update');
    Route::delete('/classes/{product}/batches/{batch}', [\App\Http\Controllers\Mentor\ClassBatchController::class, 'destroy'])->name('classes.batches.destroy');
});

Route::prefix('partner')->name('partner.')->middleware(['auth', 'verified'])->group(function (): void {
    Route::get('/dashboard', [PartnerDashboardController::class, 'index'])->name('dashboard');
    Route::get('/earnings', [\App\Http\Controllers\Partner\EarningController::class, 'index'])->name('earnings.index');


    Route::get('/profile', [\App\Http\Controllers\Partner\ProfileController::class, 'index'])->name('profile.index');
    Route::post('/profile', [\App\Http\Controllers\Partner\ProfileController::class, 'update'])->name('profile.update');

    Route::get('/withdrawals', [\App\Http\Controllers\Partner\WithdrawalController::class, 'index'])->name('withdrawals.index');
    Route::post('/withdrawals', [\App\Http\Controllers\Partner\WithdrawalController::class, 'store'])->name('withdrawals.store');



    // Project Bursa & My Projects
    Route::get('/project-bursa', [\App\Http\Controllers\Partner\ProjectBursaController::class, 'index'])->name('project-bursa.index');
    Route::get('/project-bursa/{projectRequest}', [\App\Http\Controllers\Partner\ProjectBursaController::class, 'show'])->name('project-bursa.show');
    Route::post('/project-bursa/{projectRequest}/apply', [\App\Http\Controllers\Partner\ProjectBursaController::class, 'apply'])->name('project-bursa.apply');

    Route::get('/my-projects', [\App\Http\Controllers\Partner\MyProjectController::class, 'index'])->name('my-projects.index');
    Route::get('/my-projects/{projectRequest}', [\App\Http\Controllers\Partner\MyProjectController::class, 'show'])->name('my-projects.show');
    Route::post('/my-projects/{projectRequest}/progress', [\App\Http\Controllers\Partner\MyProjectController::class, 'updateProgress'])->name('my-projects.progress');
    Route::post('/my-projects/{projectRequest}/complaint-response', [\App\Http\Controllers\Partner\MyProjectController::class, 'respondComplaint'])->name('my-projects.complaint-response');
});

Route::post('/payment/webhooks/{gateway}', PaymentWebhookController::class)->name('payment.webhooks');
