import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout';
import { Head } from '@inertiajs/react';
import KpiTile from '@/Components/KpiTile';

export default function AdminReports({ auth, kpis, alertThresholds }) {
    return (
        <AuthenticatedLayout user={auth.user}>
            <Head title="Admin - Reports" />

            <div className="py-12">
                <div className="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
                    <div>
                        <h2 className="text-2xl font-bold text-gray-900">System Reports</h2>
                        <p className="mt-1 text-sm text-gray-600">
                            Monitor system-wide KPIs and activity.
                        </p>
                    </div>

                    {/* KPIs */}
                    <div className="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-4 gap-6">
                        <KpiTile
                            title="Total Users"
                            value={kpis.total_users}
                        />
                        <KpiTile
                            title="Active Users (30d)"
                            value={kpis.active_users_30d}
                        />
                        <KpiTile
                            title="Total Records"
                            value={kpis.total_records}
                        />
                        <KpiTile
                            title="Alerts (Last 7d)"
                            value={kpis.alerts_7d}
                        />
                    </div>

                    {/* Alert Configuration */}
                    <div className="bg-white rounded-lg shadow p-6">
                        <h3 className="text-lg font-medium mb-4">Alert Thresholds (Configuration)</h3>
                        <p className="text-sm text-gray-600 mb-4">
                            These are the current alert thresholds used by the system. Future versions will allow editing these values.
                        </p>
                        <div className="grid grid-cols-2 gap-4">
                            <div className="p-4 bg-gray-50 rounded">
                                <p className="text-sm font-medium text-gray-700">LDL Threshold</p>
                                <p className="text-2xl font-semibold text-gray-900">{alertThresholds.ldl_threshold} mg/dL</p>
                            </div>
                            <div className="p-4 bg-gray-50 rounded">
                                <p className="text-sm font-medium text-gray-700">HR Elevation</p>
                                <p className="text-2xl font-semibold text-gray-900">+{alertThresholds.hr_elevation} bpm</p>
                            </div>
                            <div className="p-4 bg-gray-50 rounded">
                                <p className="text-sm font-medium text-gray-700">Sleep Minimum</p>
                                <p className="text-2xl font-semibold text-gray-900">{alertThresholds.sleep_minimum} hours</p>
                            </div>
                            <div className="p-4 bg-gray-50 rounded">
                                <p className="text-sm font-medium text-gray-700">Symptom Severity</p>
                                <p className="text-2xl font-semibold text-gray-900">{alertThresholds.symptom_severity}/10</p>
                            </div>
                        </div>
                    </div>

                    {/* System Info */}
                    <div className="bg-white rounded-lg shadow p-6">
                        <h3 className="text-lg font-medium mb-4">System Information</h3>
                        <div className="space-y-2 text-sm">
                            <div className="flex justify-between">
                                <span className="text-gray-600">Application</span>
                                <span className="font-medium">PHMS v1.0</span>
                            </div>
                            <div className="flex justify-between">
                                <span className="text-gray-600">Laravel Version</span>
                                <span className="font-medium">10.x</span>
                            </div>
                            <div className="flex justify-between">
                                <span className="text-gray-600">Queue Driver</span>
                                <span className="font-medium">Database</span>
                            </div>
                            <div className="flex justify-between">
                                <span className="text-gray-600">Timezone</span>
                                <span className="font-medium">Asia/Colombo</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </AuthenticatedLayout>
    );
}

