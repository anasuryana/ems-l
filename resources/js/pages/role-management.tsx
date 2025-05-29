import AppLayout from '@/layouts/app-layout';
import React from 'react';
import { type BreadcrumbItem, type SharedData } from '@/types';
const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Role Management',
        href: '/tes/profile',
    },
];

export default function RoleManagement() {

    return (
        <AppLayout breadcrumbs={breadcrumbs}>
            <div>
                <h1 className="text-2xl font-bold">Role Management</h1>
                <p>Ini halaman role management.</p>
            </div>
        </AppLayout>
    );
}
