import {createRouter, createWebHistory} from 'vue-router'

const router = createRouter({
    history: createWebHistory(/*import.meta.env.BASE_URL*/),
    routes: [
        {
            path: '/admin/:module/:model/:id?',
            name: 'module',
            component: () => import('../views/ModuleView.vue'),
        },
        {
            path: '/admin/profile',
            name: 'profile',
            component: () => import('../views/ProfileView.vue'),
        },
        {
            path: '/admin',
            name: 'dashboard',
            component: () => import('../views/DashboardView.vue'),
        },
    ]
});

// router.beforeEach(async (to, from, next) => {
//     const userStore = useUserStore()
//     const toast = useToast()
//     await userStore.getUserData()
//     if (userStore.auth) {
//         next()
//     } else {
//         toast.error('Время сессии истекло! Обновите страницу.')
//     }
// })

export default router
