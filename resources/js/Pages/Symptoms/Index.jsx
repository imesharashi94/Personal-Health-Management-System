import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout';
import { Head, router, useForm } from '@inertiajs/react';
import { useState } from 'react';
import DataTable from '@/Components/DataTable';

export default function SymptomsIndex({ auth, symptoms }) {
    const [showForm, setShowForm] = useState(false);
    const { data, setData, post, processing, reset } = useForm({
        name: '',
        severity: 5,
        notes: '',
        recorded_at: new Date().toISOString().split('T')[0] + 'T' + new Date().toTimeString().split(' ')[0].substring(0, 5),
    });

    const handleSubmit = (e) => {
        e.preventDefault();
        post('/symptoms', {
            onSuccess: () => {
                reset();
                setShowForm(false);
            },
        });
    };

    const handleDelete = (id) => {
        if (confirm('Are you sure you want to delete this symptom?')) {
            router.delete(`/symptoms/${id}`);
        }
    };

    const columns = [
        {
            header: 'Date & Time',
            render: (row) => new Date(row.recorded_at).toLocaleString()
        },
        { header: 'Symptom', accessor: 'name' },
        {
            header: 'Severity',
            render: (row) => (
                <div className="flex items-center gap-2">
                    <div className="w-20 bg-gray-200 rounded-full h-2">
                        <div
                            className="bg-red-600 h-2 rounded-full"
                            style={{ width: `${row.severity * 10}%` }}
                        ></div>
                    </div>
                    <span className="text-sm">{row.severity}/10</span>
                </div>
            )
        },
        { header: 'Notes', accessor: 'notes' },
        {
            header: 'Actions',
            render: (row) => (
                <button
                    onClick={() => handleDelete(row.id)}
                    className="text-red-600 hover:text-red-800 text-sm"
                >
                    Delete
                </button>
            )
        },
    ];

    return (
        <AuthenticatedLayout user={auth.user}>
            <Head title="Symptoms" />

            <div className="py-12">
                <div className="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
                    <div className="flex justify-between items-center">
                        <div>
                            <h2 className="text-2xl font-bold text-gray-900">Symptoms</h2>
                            <p className="mt-1 text-sm text-gray-600">
                                Track symptoms and their severity over time.
                            </p>
                        </div>
                        <button
                            onClick={() => setShowForm(!showForm)}
                            className="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700"
                        >
                            Log Symptom
                        </button>
                    </div>

                    {showForm && (
                        <div className="bg-white rounded-lg shadow p-6">
                            <h3 className="text-lg font-medium mb-4">Log New Symptom</h3>
                            <form onSubmit={handleSubmit} className="space-y-4">
                                <div className="grid grid-cols-2 gap-4">
                                    <div>
                                        <label className="block text-sm font-medium text-gray-700 mb-1">
                                            Symptom Name
                                        </label>
                                        <input
                                            type="text"
                                            value={data.name}
                                            onChange={(e) => setData('name', e.target.value)}
                                            required
                                            placeholder="e.g., Headache, Fatigue"
                                            className="block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                                        />
                                    </div>
                                    <div>
                                        <label className="block text-sm font-medium text-gray-700 mb-1">
                                            Date & Time
                                        </label>
                                        <input
                                            type="datetime-local"
                                            value={data.recorded_at}
                                            onChange={(e) => setData('recorded_at', e.target.value)}
                                            required
                                            className="block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                                        />
                                    </div>
                                </div>
                                <div>
                                    <label className="block text-sm font-medium text-gray-700 mb-1">
                                        Severity: {data.severity}/10
                                    </label>
                                    <input
                                        type="range"
                                        min="1"
                                        max="10"
                                        value={data.severity}
                                        onChange={(e) => setData('severity', e.target.value)}
                                        className="block w-full"
                                    />
                                </div>
                                <div>
                                    <label className="block text-sm font-medium text-gray-700 mb-1">
                                        Notes (Optional)
                                    </label>
                                    <textarea
                                        value={data.notes}
                                        onChange={(e) => setData('notes', e.target.value)}
                                        rows={3}
                                        placeholder="Additional details..."
                                        className="block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                                    />
                                </div>
                                <div className="flex justify-end gap-2">
                                    <button
                                        type="button"
                                        onClick={() => setShowForm(false)}
                                        className="px-4 py-2 border border-gray-300 rounded-md text-gray-700 hover:bg-gray-50"
                                    >
                                        Cancel
                                    </button>
                                    <button
                                        type="submit"
                                        disabled={processing}
                                        className="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 disabled:opacity-50"
                                    >
                                        Log Symptom
                                    </button>
                                </div>
                            </form>
                        </div>
                    )}

                    <div className="bg-white rounded-lg shadow overflow-hidden">
                        <DataTable
                            columns={columns}
                            data={symptoms.data}
                            emptyMessage="No symptoms logged yet."
                        />
                    </div>
                </div>
            </div>
        </AuthenticatedLayout>
    );
}

