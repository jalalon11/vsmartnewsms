import { usePage } from '@inertiajs/vue3';

/**
 * Route guard utility to check if user can access specific routes
 */
export const useRouteGuard = () => {
    const page = usePage();
    const user = page.props.auth.user;

    // Admin-only routes
    const adminRoutes = [
        'customers.index',
        'customers.create',
        'customers.show',
        'customers.edit',
        'devices.index',
        'devices.create',
        'devices.show',
        'devices.edit',
        'repair-orders.index',
        'repair-orders.create',
        'repair-orders.edit',
        'technicians.index',
        'technicians.create',
        'technicians.show',
        'technicians.edit',
        'services.index',
        'services.create',
        'services.show',
        'services.edit',
        'parts.index',
        'parts.create',
        'parts.show',
        'parts.edit',
        'invoices.index',
        'invoices.create',
        'invoices.show',
        'invoices.edit',
        'feedback.index',
        'feedback.show',
        'feedback.share',
        'feedback.toggle-featured',
        'feedback.destroy',
    ];

    // Technician-only routes
    const technicianRoutes = [
        'technician.orders',
    ];

    // Shared routes (both admin and technician can access)
    const sharedRoutes = [
        'dashboard',
        'profile.edit',
        'repair-orders.show',
        'repair-orders.update-status',
    ];

    /**
     * Check if current user can access a specific route
     * @param {string} routeName - The route name to check
     * @returns {boolean} - Whether user can access the route
     */
    const canAccessRoute = (routeName) => {
        if (!user) return false;

        // Check shared routes first
        if (sharedRoutes.includes(routeName)) {
            return true;
        }

        // Check role-specific routes
        if (user.role === 'admin') {
            return adminRoutes.includes(routeName) || sharedRoutes.includes(routeName);
        }

        if (user.role === 'technician') {
            return technicianRoutes.includes(routeName) || sharedRoutes.includes(routeName);
        }

        return false;
    };

    /**
     * Get allowed routes for current user
     * @returns {Array} - Array of route names user can access
     */
    const getAllowedRoutes = () => {
        if (!user) return [];

        let allowedRoutes = [...sharedRoutes];

        if (user.role === 'admin') {
            allowedRoutes = [...allowedRoutes, ...adminRoutes];
        } else if (user.role === 'technician') {
            allowedRoutes = [...allowedRoutes, ...technicianRoutes];
        }

        return allowedRoutes;
    };

    /**
     * Check if current user is admin
     * @returns {boolean}
     */
    const isAdmin = () => {
        return user?.role === 'admin';
    };

    /**
     * Check if current user is technician
     * @returns {boolean}
     */
    const isTechnician = () => {
        return user?.role === 'technician';
    };

    return {
        canAccessRoute,
        getAllowedRoutes,
        isAdmin,
        isTechnician,
        user,
    };
};
