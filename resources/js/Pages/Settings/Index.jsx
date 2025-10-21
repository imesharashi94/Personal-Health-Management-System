import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout';
import { Head } from '@inertiajs/react';

export default function SettingsIndex({ auth, user, consents }) {
    return (
        <AuthenticatedLayout user={auth.user}>
            <Head title="Settings" />

            <div className="py-12">
                <div className="max-w-4xl mx-auto sm:px-6 lg:px-8 space-y-6">
                    <div>
                        <h2 className="text-2xl font-bold text-gray-900">Settings</h2>
                        <p className="mt-1 text-sm text-gray-600">
                            Manage your account settings and preferences.
                        </p>
                    </div>

                    {/* Profile Section */}
                    <div className="bg-white rounded-lg shadow p-6">
                        <h3 className="text-lg font-medium mb-4">Profile Information</h3>
                        <div className="space-y-3">
                            <div>
                                <label className="block text-sm font-medium text-gray-700">Name</label>
                                <p className="mt-1 text-sm text-gray-900">{user.name}</p>
                            </div>
                            <div>
                                <label className="block text-sm font-medium text-gray-700">Email</label>
                                <p className="mt-1 text-sm text-gray-900">{user.email}</p>
                            </div>
                            <div>
                                <label className="block text-sm font-medium text-gray-700">Timezone</label>
                                <p className="mt-1 text-sm text-gray-900">{user.timezone}</p>
                            </div>
                            <div>
                                <label className="block text-sm font-medium text-gray-700">Role</label>
                                <p className="mt-1 text-sm text-gray-900 capitalize">{user.role}</p>
                            </div>
                        </div>
                    </div>

                    {/* Consents & Privacy */}
                    <div className="bg-white rounded-lg shadow p-6">
                        <h3 className="text-lg font-medium mb-4">Data Consents & Privacy</h3>
                        <p className="text-sm text-gray-600 mb-4">
                            Manage third-party data access and privacy settings.
                        </p>
                        {consents && consents.length > 0 ? (
                            <div className="space-y-3">
                                {consents.map((consent) => (
                                    <div key={consent.id} className="flex justify-between items-center p-3 border rounded">
                                        <div>
                                            <p className="text-sm font-medium">{consent.provider}</p>
                                            <p className="text-xs text-gray-500">
                                                Granted: {new Date(consent.granted_at).toLocaleDateString()}
                                            </p>
                                        </div>
                                        <span className={`px-2 py-1 text-xs rounded ${
                                            consent.revoked_at ? 'bg-gray-100 text-gray-800' : 'bg-green-100 text-green-800'
                                        }`}>
                                            {consent.revoked_at ? 'Revoked' : 'Active'}
                                        </span>
                                    </div>
                                ))}
                            </div>
                        ) : (
                            <p className="text-sm text-gray-500">No third-party consents granted.</p>
                        )}
                    </div>

                    {/* Security */}
                    <div className="bg-white rounded-lg shadow p-6">
                        <h3 className="text-lg font-medium mb-4">Security</h3>
                        <div className="space-y-3">
                            <div>
                                <label className="block text-sm font-medium text-gray-700">Two-Factor Authentication</label>
                                <p className="mt-1 text-sm text-gray-500">
                                    Add an extra layer of security to your account (coming soon).
                                </p>
                            </div>
                            <div>
                                <label className="block text-sm font-medium text-gray-700">Password</label>
                                <button className="mt-1 text-sm text-blue-600 hover:text-blue-800">
                                    Change Password
                                </button>
                            </div>
                        </div>
                    </div>

                    {/* Danger Zone */}
                    <div className="bg-white rounded-lg shadow p-6 border-red-200 border">
                        <h3 className="text-lg font-medium text-red-900 mb-4">Danger Zone</h3>
                        <div className="space-y-3">
                            <div>
                                <label className="block text-sm font-medium text-gray-700">Delete All Data</label>
                                <p className="mt-1 text-sm text-gray-500 mb-2">
                                    Permanently delete all your health data. This action cannot be undone.
                                </p>
                                <button className="text-sm text-red-600 hover:text-red-800 font-medium">
                                    Delete My Data
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </AuthenticatedLayout>
    );
}

