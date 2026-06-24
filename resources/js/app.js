import '../css/app.css';
import 'sweetalert2/dist/sweetalert2.min.css';

import { createInertiaApp, router } from '@inertiajs/vue3';
import { createApp, h } from 'vue';
import Swal from 'sweetalert2';

const namedRoutes = {
    'coaching.index': '/coaching',
    'coaching.show': '/coaching/:slug',
    'coaching.checkout.show': '/coaching/:slug/checkout',
    'coaching.checkout.store': '/coaching/:slug/checkout',
    'member.my-coaching.index': '/member/coaching',
    'member.my-coaching.show': '/member/coaching/:coachingBooking',
    'admin.coaching-topics.index': '/admin/coaching-topics',
    'admin.coaching-topics.store': '/admin/coaching-topics',
    'admin.coaching-topics.update': '/admin/coaching-topics/:coachingTopic',
    'admin.coaching-topics.destroy': '/admin/coaching-topics/:coachingTopic',
    'admin.coaching-services.index': '/admin/coaching-services',
    'admin.coaching-services.create': '/admin/coaching-services/create',
    'admin.coaching-services.store': '/admin/coaching-services',
    'admin.coaching-services.edit': '/admin/coaching-services/:coachingService/edit',
    'admin.coaching-services.update': '/admin/coaching-services/:coachingService',
    'admin.coaching-services.destroy': '/admin/coaching-services/:coachingService',
    'admin.payment-verifications.update': '/admin/payment-verifications/:transaction',
    'partner.coaching-services.index': '/partner/coaching-services',
    'partner.coaching-services.create': '/partner/coaching-services/create',
    'partner.coaching-services.store': '/partner/coaching-services',
    'partner.coaching-services.edit': '/partner/coaching-services/:coachingService/edit',
    'partner.coaching-services.update': '/partner/coaching-services/:coachingService',
    'partner.coaching-services.destroy': '/partner/coaching-services/:coachingService',
    
    // Project Requests (Member)
    'member.project-requests.index': '/member/project-requests',
    'member.project-requests.create': '/member/project-requests/create',
    'member.project-requests.store': '/member/project-requests',
    'member.project-requests.show': '/member/project-requests/:projectRequest',
    'member.project-requests.edit': '/member/project-requests/:projectRequest/edit',
    'member.project-requests.update': '/member/project-requests/:projectRequest',
    'member.project-requests.complete': '/member/project-requests/:projectRequest/complete',
    'member.project-requests.pay': '/member/project-requests/:projectRequest/pay',
    'member.project-requests.process-payment': '/member/project-requests/:projectRequest/pay',
    'member.project-requests.complaint': '/member/project-requests/:projectRequest/complaint',
    'member.project-requests.complaint.store': '/member/project-requests/:projectRequest/complaint',

    // Project Requests (Admin)
    'admin.project-requests.index': '/admin/project-requests',
    'admin.project-requests.show': '/admin/project-requests/:project',
    'admin.project-requests.approve': '/admin/project-requests/:project/approve',
    'admin.project-requests.reject': '/admin/project-requests/:project/reject',
    'admin.project-requests.assign': '/admin/project-requests/:project/assign',
    'admin.project-requests.destroy': '/admin/project-requests/:project',
    'admin.project-complaints.resolve': '/admin/project-complaints/:complaint/resolve',

    // Project Bursa & My Projects (Partner)
    'partner.project-bursa.index': '/partner/project-bursa',
    'partner.project-bursa.show': '/partner/project-bursa/:project',
    'partner.project-bursa.apply': '/partner/project-bursa/:project/apply',
    'partner.my-projects.index': '/partner/my-projects',
    'partner.my-projects.show': '/partner/my-projects/:project',
    'partner.my-projects.progress': '/partner/my-projects/:project/progress',
    'partner.my-projects.complaint-response': '/partner/my-projects/:project/complaint-response',
    
    // Member Transactions
    'member.transactions.history': '/member/transactions/history',
    'member.transactions.refund.store': '/member/transactions/:transaction/refund',
    
    // Admin Refund
    'admin.refund-requests.process': '/admin/refund-requests/:refundRequest/process',
};

const normalizeRouteParams = (params) => {
    if (params === undefined || params === null) {
        return {};
    }

    if (typeof params === 'object' && !Array.isArray(params)) {
        return params;
    }

    return { value: params };
};

window.route ??= (name, params) => {
    let path = namedRoutes[name];

    if (!path) {
        throw new Error(`Route [${name}] is not defined in the frontend route map.`);
    }

    const routeParams = normalizeRouteParams(params);
    const positionalValue = routeParams.value;

    path = path.replace(/:([A-Za-z0-9_]+)/g, (match, key) => {
        const value = routeParams[key] ?? routeParams[key.replace(/[A-Z]/g, (letter) => `_${letter.toLowerCase()}`)] ?? positionalValue;

        if (value === undefined || value === null) {
            throw new Error(`Missing parameter [${key}] for route [${name}].`);
        }

        return encodeURIComponent(value);
    });

    return path;
};

const pages = import.meta.glob('./Pages/**/*.vue', { eager: true });

const showFlashNotification = (flash) => {
    if (!flash?.success && !flash?.error) return;

    Swal.fire({
        title: flash.success ? 'Berhasil' : 'Gagal',
        text: flash.success || flash.error,
        icon: flash.success ? 'success' : 'error',
        confirmButtonColor: '#0284c7',
        confirmButtonText: 'OK',
    });
};

createInertiaApp({
    resolve: (name) => pages[`./Pages/${name}.vue`],
    setup({ el, App, props, plugin }) {
        const app = createApp({ render: () => h(App, props) });
        app.config.globalProperties.route = window.route;
        app.use(plugin).mount(el);

        showFlashNotification(props.initialPage?.props?.flash);

        router.on('success', (event) => {
            showFlashNotification(event.detail.page.props.flash);
        });
    },
    progress: {
        color: '#0284c7',
    },
});
