import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout';
import { Head } from '@inertiajs/react';
import DataTable from '@/Components/DataTable';

export default function AdminAudit({ auth, logs, filters }) {
    const columns = [
        { header: 'ID', accessor: 'id' },
        {
            header: 'User',
            render: (row) => row.user ? `${row.user.name} (${row.user.email})` : 'System'
        },
        {
            header: 'Action',
            render: (row) => (
                <span className="px-2 py-1 text-xs font-medium rounded bg-blue-100 text-blue-800">
                    {row.action}
                </span>
            )
        },
        {
            header: 'Entity',
            render: (row) => `${row.entity} #${row.entity_id}`
        },
        {
            header: 'Timestamp',
            render: (row) => new Date(row.created_at).toLocaleString()
        },
    ];

    return (
        <AuthenticatedLayout user={auth.user}>
            <Head title="Admin - Audit Logs" />

            <div className="py-12">
                <div className="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
                    <div>
                        <h2 className="text-2xl font-bold text-gray-900">Audit Logs</h2>
                        <p className="mt-1 text-sm text-gray-600">
                            Track all system actions and user activities.
                        </p>
                    </div>

                    {/* Filters */}
                    <div className="bg-white rounded-lg shadow p-4">
                        <form method="GET" action="/admin/audit">
                            <div className="grid grid-cols-3 gap-4">
                                <div>
                                    <label className="block text-sm font-medium text-gray-700 mb-1">
                                        Entity
                                    </label>
                                    <input
                                        type="text"
                                        name="entity"
                                        defaultValue={filters?.entity || ''}
                                        placeholder="e.g., lab_report"
                                        className="block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                                    />
                                </div>
                                <div>
                                    <label className="block text-sm font-medium text-gray-700 mb-1">
                                        Action
                                    </label>
                                    <input
                                        type="text"
                                        name="action"
                                        defaultValue={filters?.action || ''}
                                        placeholder="e.g., view, create"
                                        className="block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                                    />
                                </div>
                                <div className="flex items-end">
                                    <button
                                        type="submit"
                                        className="w-full px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700"
                                    >
                                        Filter
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>

                    <div className="bg-white rounded-lg shadow overflow-hidden">
                        <DataTable
                            columns={columns}
                            data={logs.data}
                            emptyMessage="No audit logs found."
                        />
                    </div>

                    {/* Pagination */}
                    {logs.last_page > 1 && (
                        <div className="bg-white rounded-lg shadow px-6 py-4">
                            <div className="flex justify-between items-center text-sm">
                                <p className="text-gray-600">
                                    Showing {logs.from} to {logs.to} of {logs.total} logs
                                </p>
                                <div className="flex gap-2">
                                    {logs.links.map((link, idx) => (
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

