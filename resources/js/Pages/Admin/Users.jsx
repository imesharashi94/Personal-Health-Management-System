import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout';
import { Head } from '@inertiajs/react';
import DataTable from '@/Components/DataTable';

export default function AdminUsers({ auth, users, filters }) {
    const columns = [
        { header: 'ID', accessor: 'id' },
        {
            header: 'Name',
            render: (row) => (
                <div>
                    <p className="font-medium">{row.name}</p>
                    <p className="text-xs text-gray-500">{row.email}</p>
                </div>
            )
        },
        {
            header: 'Role',
            render: (row) => (
                <span className={`px-2 py-1 text-xs font-medium rounded capitalize ${
                    row.role === 'admin' ? 'bg-purple-100 text-purple-800' : 'bg-gray-100 text-gray-800'
                }`}>
                    {row.role}
                </span>
            )
        },
        {
            header: 'Metrics',
            render: (row) => (
                <span className="text-sm">{row.health_metrics_count || 0}</span>
            )
        },
        {
            header: 'Lab Reports',
            render: (row) => (
                <span className="text-sm">{row.lab_reports_count || 0}</span>
            )
        },
        {
            header: 'Joined',
            render: (row) => new Date(row.created_at).toLocaleDateString()
        },
        {
            header: 'Status',
            render: (row) => (
                <span className={`px-2 py-1 text-xs font-medium rounded ${
                    row.email_verified_at ? 'bg-green-100 text-green-800' : 'bg-yellow-100 text-yellow-800'
                }`}>
                    {row.email_verified_at ? 'Verified' : 'Pending'}
                </span>
            )
        },
    ];

    return (
        <AuthenticatedLayout user={auth.user}>
            <Head title="Admin - Users" />

            <div className="py-12">
                <div className="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
                    <div>
                        <h2 className="text-2xl font-bold text-gray-900">User Management</h2>
                        <p className="mt-1 text-sm text-gray-600">
                            View and manage all users in the system.
                        </p>
                    </div>

                    {/* Search Bar */}
                    <div className="bg-white rounded-lg shadow p-4">
                        <form method="GET" action="/admin/users">
                            <div className="flex gap-2">
                                <input
                                    type="text"
                                    name="search"
                                    defaultValue={filters?.search || ''}
                                    placeholder="Search by name or email..."
                                    className="flex-1 rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                                />
                                <button
                                    type="submit"
                                    className="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700"
                                >
                                    Search
                                </button>
                            </div>
                        </form>
                    </div>

                    <div className="bg-white rounded-lg shadow overflow-hidden">
                        <DataTable
                            columns={columns}
                            data={users.data}
                            emptyMessage="No users found."
                        />
                    </div>

                    {/* Pagination */}
                    {users.last_page > 1 && (
                        <div className="bg-white rounded-lg shadow px-6 py-4">
                            <div className="flex justify-between items-center text-sm">
                                <p className="text-gray-600">
                                    Showing {users.from} to {users.to} of {users.total} users
                                </p>
                                <div className="flex gap-2">
                                    {users.links.map((link, idx) => (
                                        <a
                                            key={idx}
                                            href={link.url || '#'}
                                            className={`px-3 py-1 rounded ${
                                                link.active
                                                    ? 'bg-blue-600 text-white'
                                                    : link.url
                                                    ? 'bg-gray-100 text-gray-700 hover:bg-gray-200'
                                                    : 'bg-gray-100 text-gray-400 cursor-not-allowed'
                                            }`}
                                            dangerouslySetInnerHTML={{ __html: link.label }}
                                        />
                                    ))}
                                </div>
                            </div>
                        </div>
                    )}
                </div>
            </div>
        </AuthenticatedLayout>
    );
}

