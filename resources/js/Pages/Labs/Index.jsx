import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout';
import { Head, router } from '@inertiajs/react';
import { useState } from 'react';
import FileDropzone from '@/Components/FileDropzone';

export default function LabsIndex({ auth, reports }) {
    const [showUpload, setShowUpload] = useState(false);
    const [uploading, setUploading] = useState(false);
    const [formData, setFormData] = useState({
        file: null,
        report_date: '',
        facility: '',
    });

    const handleUpload = (acceptedFiles) => {
        if (acceptedFiles && acceptedFiles[0]) {
            setFormData({ ...formData, file: acceptedFiles[0] });
        }
    };

    const handleSubmit = (e) => {
        e.preventDefault();
        setUploading(true);

        const data = new FormData();
        data.append('file', formData.file);
        if (formData.report_date) data.append('report_date', formData.report_date);
        if (formData.facility) data.append('facility', formData.facility);

        router.post('/labs/upload', data, {
            onFinish: () => {
                setUploading(false);
                setShowUpload(false);
                setFormData({ file: null, report_date: '', facility: '' });
            },
        });
    };

    return (
        <AuthenticatedLayout user={auth.user}>
            <Head title="Lab Reports" />

            <div className="py-12">
                <div className="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
                    <div className="flex justify-between items-center">
                        <div>
                            <h2 className="text-2xl font-bold text-gray-900">Lab Reports</h2>
                            <p className="mt-1 text-sm text-gray-600">
                                Upload and manage your lab reports with OCR extraction.
                            </p>
                        </div>
                        <button
                            onClick={() => setShowUpload(!showUpload)}
                            className="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700"
                        >
                            Upload Report
                        </button>
                    </div>

                    {showUpload && (
                        <div className="bg-white rounded-lg shadow p-6">
                            <h3 className="text-lg font-medium mb-4">Upload Lab Report</h3>
                            <form onSubmit={handleSubmit} className="space-y-4">
                                <FileDropzone
                                    onDrop={handleUpload}
                                    accept={{ 'application/pdf': ['.pdf'], 'image/*': ['.png', '.jpg', '.jpeg'] }}
                                />
                                {formData.file && (
                                    <p className="text-sm text-green-600">Selected: {formData.file.name}</p>
                                )}
                                <div className="grid grid-cols-2 gap-4">
                                    <div>
                                        <label className="block text-sm font-medium text-gray-700 mb-1">
                                            Report Date
                                        </label>
                                        <input
                                            type="date"
                                            value={formData.report_date}
                                            onChange={(e) => setFormData({ ...formData, report_date: e.target.value })}
                                            className="block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                                        />
                                    </div>
                                    <div>
                                        <label className="block text-sm font-medium text-gray-700 mb-1">
                                            Facility
                                        </label>
                                        <input
                                            type="text"
                                            value={formData.facility}
                                            onChange={(e) => setFormData({ ...formData, facility: e.target.value })}
                                            placeholder="Hospital or clinic name"
                                            className="block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                                        />
                                    </div>
                                </div>
                                <div className="flex justify-end gap-2">
                                    <button
                                        type="button"
                                        onClick={() => setShowUpload(false)}
                                        className="px-4 py-2 border border-gray-300 rounded-md text-gray-700 hover:bg-gray-50"
                                    >
                                        Cancel
                                    </button>
                                    <button
                                        type="submit"
                                        disabled={!formData.file || uploading}
                                        className="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 disabled:opacity-50"
                                    >
                                        {uploading ? 'Uploading...' : 'Upload & Parse'}
                                    </button>
                                </div>
                            </form>
                        </div>
                    )}

                    {/* Reports List */}
                    <div className="bg-white rounded-lg shadow">
                        {reports.data && reports.data.length > 0 ? (
                            <ul className="divide-y divide-gray-200">
                                {reports.data.map((report) => (
                                    <li key={report.id} className="p-6 hover:bg-gray-50">
                                        <a href={`/labs/${report.id}`} className="block">
                                            <div className="flex justify-between items-start">
                                                <div className="flex-1">
                                                    <p className="text-sm font-medium text-gray-900">
                                                        {report.facility || 'Lab Report'}
                                                    </p>
                                                    <p className="text-sm text-gray-600">
                                                        {report.report_date ? new Date(report.report_date).toLocaleDateString() : 'No date'}
                                                    </p>
                                                    <div className="mt-2 flex flex-wrap gap-2">
                                                        {report.observations?.slice(0, 3).map((observation) => (
                                                            <span key={observation.id} className={`inline-flex items-center px-2 py-1 rounded text-xs font-medium ${observation.flagged ? 'bg-red-100 text-red-800' : 'bg-gray-100 text-gray-800'}`}>
                                                                {observation.metric_name}: {observation.value} {observation.unit}
                                                            </span>
                                                        ))}
                                                        {report.observations?.length > 3 && (
                                                            <span className="text-xs text-gray-500">+{report.observations.length - 3} more</span>
                                                        )}
                                                    </div>
                                                </div>
                                                <span className={`ml-4 px-2 py-1 text-xs font-medium rounded ${
                                                    report.status === 'parsed' ? 'bg-green-100 text-green-800' :
                                                    report.status === 'failed' ? 'bg-red-100 text-red-800' :
                                                    'bg-yellow-100 text-yellow-800'
                                                }`}>
                                                    {report.status}
                                                </span>
                                            </div>
                                        </a>
                                    </li>
                                ))}
                            </ul>
                        ) : (
                            <div className="p-12 text-center text-gray-500">
                                No lab reports yet. Upload your first report to get started.
                            </div>
                        )}
                    </div>
                </div>
            </div>
        </AuthenticatedLayout>
    );
}

