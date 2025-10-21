import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout';
import { Head, useForm } from '@inertiajs/react';
import { useState } from 'react';
import DateRangePicker from '@/Components/DateRangePicker';

export default function ExportIndex({ auth, exports }) {
    const [showForm, setShowForm] = useState(false);
    
    const { data, setData, post, processing } = useForm({
        start_date: new Date(Date.now() - 30 * 24 * 60 * 60 * 1000).toISOString().split('T')[0],
        end_date: new Date().toISOString().split('T')[0],
        metric_types: ['steps', 'hr', 'sleep'],
        format: 'csv',
    });

    const handleSubmit = (e) => {
        e.preventDefault();
        post('/export/run', {
            onSuccess: () => setShowForm(false),
        });
    };

    const toggleMetric = (metric) => {
        if (data.metric_types.includes(metric)) {
            setData('metric_types', data.metric_types.filter(m => m !== metric));
        } else {
            setData('metric_types', [...data.metric_types, metric]);
        }
    };

    return (
        <AuthenticatedLayout user={auth.user}>
            <Head title="Export Data" />

            <div className="py-12">
                <div className="max-w-4xl mx-auto sm:px-6 lg:px-8 space-y-6">
                    <div className="flex justify-between items-center">
                        <div>
                            <h2 className="text-2xl font-bold text-gray-900">Export Health Data</h2>
                            <p className="mt-1 text-sm text-gray-600">
                                Download your health data for backup or analysis.
                            </p>
                        </div>
                        <button
                            onClick={() => setShowForm(!showForm)}
                            className="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700"
                        >
                            New Export
                        </button>
                    </div>

                    {showForm && (
                        <div className="bg-white rounded-lg shadow p-6">
                            <h3 className="text-lg font-medium mb-4">Create Export</h3>
                            <form onSubmit={handleSubmit} className="space-y-4">
                                <DateRangePicker
                                    startDate={data.start_date}
                                    endDate={data.end_date}
                                    onStartDateChange={(value) => setData('start_date', value)}
                                    onEndDateChange={(value) => setData('end_date', value)}
                                    label="Date Range"
                                />

                                <div>
                                    <label className="block text-sm font-medium text-gray-700 mb-2">
                                        Data Types to Export
                                    </label>
                                    <div className="grid grid-cols-2 gap-2">
                                        {['steps', 'hr', 'sleep', 'labs', 'symptoms', 'medications'].map((metric) => (
                                            <label key={metric} className="flex items-center space-x-2">
                                                <input
                                                    type="checkbox"
                                                    checked={data.metric_types.includes(metric)}
                                                    onChange={() => toggleMetric(metric)}
                                                    className="rounded border-gray-300 text-blue-600 focus:ring-blue-500"
                                                />
                                                <span className="text-sm text-gray-700 capitalize">{metric}</span>
                                            </label>
                                        ))}
                                    </div>
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
                                        disabled={processing || data.metric_types.length === 0}
                                        className="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 disabled:opacity-50"
                                    >
                                        {processing ? 'Generating...' : 'Generate Export'}
                                    </button>
                                </div>
                            </form>
                        </div>
                    )}

                    <div className="bg-white rounded-lg shadow">
                        <div className="px-6 py-4 border-b border-gray-200">
                            <h3 className="text-lg font-medium">Recent Exports</h3>
                        </div>
                        {exports && exports.length > 0 ? (
                            <ul className="divide-y divide-gray-200">
                                {exports.map((exportJob) => (
                                    <li key={exportJob.id} className="p-6">
                                        <div className="flex justify-between items-start">
                                            <div>
                                                <p className="text-sm font-medium text-gray-900">
                                                    Export {exportJob.id}
                                                </p>
                                                <p className="text-xs text-gray-500 mt-1">
                                                    Created: {new Date(exportJob.created_at).toLocaleString()}
                                                </p>
                                                {exportJob.completed_at && (
                                                    <p className="text-xs text-gray-500">
                                                        Completed: {new Date(exportJob.completed_at).toLocaleString()}
                                                    </p>
                                                )}
                                            </div>
                                            <div className="flex items-center gap-3">
                                                <span className={`px-2 py-1 text-xs font-medium rounded ${
                                                    exportJob.status === 'done' ? 'bg-green-100 text-green-800' :
                                                    exportJob.status === 'failed' ? 'bg-red-100 text-red-800' :
                                                    'bg-yellow-100 text-yellow-800'
                                                }`}>
                                                    {exportJob.status}
                                                </span>
                                                {exportJob.status === 'done' && (
                                                    <a
                                                        href={`/export/${exportJob.id}/download`}
                                                        className="text-sm text-blue-600 hover:text-blue-800"
                                                    >
                                                        Download
                                                    </a>
                                                )}
                                            </div>
                                        </div>
                                    </li>
                                ))}
                            </ul>
                        ) : (
                            <div className="p-12 text-center text-gray-500">
                                No exports yet. Create your first export above.
                            </div>
                        )}
                    </div>
                </div>
            </div>
        </AuthenticatedLayout>
    );
}

