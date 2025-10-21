import { Head, Link } from '@inertiajs/react';

export default function Home({ auth }) {
    return (
        <>
            <Head title="Welcome to PHMS" />
            
            <div className="min-h-screen bg-gradient-to-br from-blue-50 to-indigo-100">
                <div className="relative sm:flex sm:justify-center sm:items-center min-h-screen">
                    <div className="max-w-7xl mx-auto p-6 lg:p-8">
                        <div className="text-center">
                            {/* Logo/Icon */}
                            <div className="flex justify-center mb-8">
                                <div className="w-20 h-20 bg-blue-600 rounded-full flex items-center justify-center">
                                    <svg className="w-12 h-12 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z" />
                                    </svg>
                                </div>
                            </div>

                            {/* Title */}
                            <h1 className="text-5xl font-bold text-gray-900 mb-4">
                                Personal Health Management System
                            </h1>
                            <p className="text-xl text-gray-600 mb-8 max-w-2xl mx-auto">
                                Track, manage, and analyze your health data in one centralized platform. 
                                Import fitness data, upload lab reports with OCR, and get intelligent health insights.
                            </p>

                            {/* Auth Buttons */}
                            <div className="flex justify-center gap-4 mb-12">
                                {auth.user ? (
                                    <Link
                                        href="/dashboard"
                                        className="px-8 py-3 bg-blue-600 text-white rounded-lg font-semibold hover:bg-blue-700 transition"
                                    >
                                        Go to Dashboard
                                    </Link>
                                ) : (
                                    <>
                                        <Link
                                            href="/login"
                                            className="px-8 py-3 bg-blue-600 text-white rounded-lg font-semibold hover:bg-blue-700 transition"
                                        >
                                            Login
                                        </Link>
                                        <Link
                                            href="/register"
                                            className="px-8 py-3 bg-white text-blue-600 border-2 border-blue-600 rounded-lg font-semibold hover:bg-blue-50 transition"
                                        >
                                            Register
                                        </Link>
                                    </>
                                )}
                            </div>

                            {/* Features */}
                            <div className="grid md:grid-cols-3 gap-8 mt-16">
                                <div className="bg-white p-6 rounded-lg shadow-md">
                                    <div className="w-12 h-12 bg-green-100 rounded-lg flex items-center justify-center mx-auto mb-4">
                                        <svg className="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                                        </svg>
                                    </div>
                                    <h3 className="text-lg font-semibold mb-2">Health Metrics</h3>
                                    <p className="text-gray-600 text-sm">Import and track steps, heart rate, sleep, and more with interactive charts.</p>
                                </div>

                                <div className="bg-white p-6 rounded-lg shadow-md">
                                    <div className="w-12 h-12 bg-purple-100 rounded-lg flex items-center justify-center mx-auto mb-4">
                                        <svg className="w-6 h-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                        </svg>
                                    </div>
                                    <h3 className="text-lg font-semibold mb-2">Lab Reports OCR</h3>
                                    <p className="text-gray-600 text-sm">Upload PDF/JPG lab reports and automatically extract values using OCR.</p>
                                </div>

                                <div className="bg-white p-6 rounded-lg shadow-md">
                                    <div className="w-12 h-12 bg-red-100 rounded-lg flex items-center justify-center mx-auto mb-4">
                                        <svg className="w-6 h-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
                                        </svg>
                                    </div>
                                    <h3 className="text-lg font-semibold mb-2">Smart Alerts</h3>
                                    <p className="text-gray-600 text-sm">Get intelligent alerts based on your health trends and patterns.</p>
                                </div>
                            </div>

                            {/* Demo Credentials */}
                            <div className="mt-12 p-6 bg-yellow-50 border border-yellow-200 rounded-lg max-w-xl mx-auto">
                                <h4 className="font-semibold text-yellow-900 mb-2">🎯 Demo Credentials</h4>
                                <div className="text-sm text-yellow-800 space-y-1">
                                    <p><strong>User:</strong> demo@phms.test / password</p>
                                    <p><strong>Admin:</strong> admin@phms.test / password</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </>
    );
}
