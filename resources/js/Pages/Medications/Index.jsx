import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout';
import { Head, router, useForm } from '@inertiajs/react';
import { useState } from 'react';

export default function MedicationsIndex({ auth, medications }) {
    const [showForm, setShowForm] = useState(false);
    const { data, setData, post, processing, reset } = useForm({
        name: '',
        dose: '',
        unit: '',
        notes: '',
    });

    const handleSubmit = (e) => {
        e.preventDefault();
        post('/medications', {
            onSuccess: () => {
                reset();
                setShowForm(false);
            },
        });
    };

    const handleDelete = (id) => {
        if (confirm('Are you sure you want to delete this medication?')) {
            router.delete(`/medications/${id}`);
        }
    };

    return (
        <AuthenticatedLayout user={auth.user}>
            <Head title="Medications" />

            <div className="py-12">
                <div className="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
                    <div className="flex justify-between items-center">
                        <div>
                            <h2 className="text-2xl font-bold text-gray-900">Medications</h2>
                            <p className="mt-1 text-sm text-gray-600">
                                Manage your medications and schedules.
                            </p>
                        </div>
                        <button
                            onClick={() => setShowForm(!showForm)}
                            className="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700"
                        >
                            Add Medication
                        </button>
                    </div>

                    {showForm && (
                        <div className="bg-white rounded-lg shadow p-6">
                            <h3 className="text-lg font-medium mb-4">Add New Medication</h3>
                            <form onSubmit={handleSubmit} className="space-y-4">
                                <div className="grid grid-cols-3 gap-4">
                                    <div className="col-span-2">
                                        <label className="block text-sm font-medium text-gray-700 mb-1">
                                            Medication Name
                                        </label>
                                        <input
                                            type="text"
                                            value={data.name}
                                            onChange={(e) => setData('name', e.target.value)}
                                            required
                                            placeholder="e.g., Aspirin, Metformin"
                                            className="block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                                        />
                                    </div>
                                    <div>
                                        <label className="block text-sm font-medium text-gray-700 mb-1">
                                            Dose
                                        </label>
                                        <div className="flex gap-2">
                                            <input
                                                type="number"
                                                step="0.01"
                                                value={data.dose}
                                                onChange={(e) => setData('dose', e.target.value)}
                                                placeholder="50"
                                                className="block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                                            />
                                            <input
                                                type="text"
                                                value={data.unit}
                                                onChange={(e) => setData('unit', e.target.value)}
                                                placeholder="mg"
                                                className="block w-20 rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                                            />
                                        </div>
                                    </div>
                                </div>
                                <div>
                                    <label className="block text-sm font-medium text-gray-700 mb-1">
                                        Notes (Optional)
                                    </label>
                                    <textarea
                                        value={data.notes}
                                        onChange={(e) => setData('notes', e.target.value)}
                                        rows={2}
                                        placeholder="Take with food, etc."
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
                                        Add Medication
                                    </button>
                                </div>
                            </form>
                        </div>
                    )}

                    <div className="grid gap-4">
                        {medications && medications.length > 0 ? (
                            medications.map((medication) => (
                                <div key={medication.id} className="bg-white rounded-lg shadow p-6">
                                    <div className="flex justify-between items-start">
                                        <div>
                                            <h3 className="text-lg font-medium text-gray-900">
                                                {medication.name}
                                            </h3>
                                            {medication.dose && (
                                                <p className="text-sm text-gray-600">
                                                    {medication.dose} {medication.unit}
                                                </p>
                                            )}
                                            {medication.notes && (
                                                <p className="mt-1 text-sm text-gray-500">{medication.notes}</p>
                                            )}
                                        </div>
                                        <button
                                            onClick={() => handleDelete(medication.id)}
                                            className="text-red-600 hover:text-red-800 text-sm"
                                        >
                                            Delete
                                        </button>
                                    </div>
                                    {medication.schedules && medication.schedules.length > 0 && (
                                        <div className="mt-4 border-t pt-4">
                                            <h4 className="text-sm font-medium text-gray-700 mb-2">Schedules</h4>
                                            <div className="space-y-2">
                                                {medication.schedules.map((schedule) => (
                                                    <div key={schedule.id} className="flex items-center justify-between text-sm">
                                                        <span>
                                                            {schedule.frequency} at {schedule.time_of_day}
                                                        </span>
                                                        <span className={`px-2 py-1 rounded text-xs ${
                                                            schedule.active ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800'
                                                        }`}>
                                                            {schedule.active ? 'Active' : 'Paused'}
                                                        </span>
                                                    </div>
                                                ))}
                                            </div>
                                        </div>
                                    )}
                                </div>
                            ))
                        ) : (
                            <div className="bg-white rounded-lg shadow p-12 text-center text-gray-500">
                                No medications added yet.
                            </div>
                        )}
                    </div>
                </div>
            </div>
        </AuthenticatedLayout>
    );
}

