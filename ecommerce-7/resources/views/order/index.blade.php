<x-app-layout :title="'Daftar Order'">
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Daftar Order') }}
        </h2>
    </x-slot>
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 px-2 grid gap-4">
            <div class="mb-[20px]">
            @include('layouts.success-error-msg')
            </div>
            <div class="bg-white overflow-hidden shadow-sm rounded-lg p-6">
                <h3 class="text-lg font-semibold text-gray-800 mb-4">Order Terbaru</h3>
                {{-- table for latest orders --}}
                <div class="overflow-x-auto">
                    <table class="min-w-[640px] w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Order Number</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nama Customer</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Total Harga</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tanggal</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @forelse($orders as $order)
                                <tr>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <a href="{{ route('order.show', $order->order_number) }}" class="text-blue-500 hover:underline"> {{ $order->order_number }}</a>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">{{ $order->user->name }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap">Rp{{ number_format($order->total_amount, 0, ',', '.') }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        
                                        <a href="#" 
                                        @if(Auth::user()->role == 'admin')
                                        x-data=""
                                        x-on:click.prevent="$dispatch('open-modal', 'edit-order-status.{{ $order->id }}')"
                                        @endif
                                        class="inline-flex items-center px-2 py-1 rounded text-xs font-medium {{ $order->status == 'pending' ? 'bg-yellow-100 text-yellow-800' : ($order->status == 'processing' ? 'bg-blue-100 text-blue-800' : ($order->status == 'completed' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800')) }}">
                                        {{ ucfirst($order->status) }}
                                        </a>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">{{ $order->created_at->format('d M Y H:i') }}</td>
                                </tr>
                                @if(Auth::user()->role == 'admin')
                                @push('scripts')
                                    <x-modal name="edit-order-status.{{ $order->id }}" :title="'Update Status Order ' . $order->order_number">
                                        <form action="{{ route('orders.updateStatus', $order) }}" method="POST" class="space-y-4 p-4">
                                            @csrf
                                            <div>
                                                <label for="status" class="block text-sm font-medium text-gray-700">Status</label>
                                                <select id="status" name="status" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                                                    <option value="pending" {{ $order->status == 'pending' ? 'selected' : '' }}>Pending</option>
                                                    <option value="processing" {{ $order->status == 'processing' ? 'selected' : '' }}>Processing</option>
                                                    <option value="completed" {{ $order->status == 'completed' ? 'selected' : '' }}>Completed</option>
                                                    <option value="cancelled" {{ $order->status == 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                                                </select>
                                            </div>
                                            <div class="flex justify-end">
                                                <x-secondary-button x-on:click="$dispatch('close')">Batal</x-secondary-button>
                                                <x-primary-button type="submit" class="ml-2">Simpan</x-primary-button>
                                            </div>
                                        </form>
                                    </x-modal>
                                @endpush
                                @endif
                            @empty
                                <tr>
                                    <td colspan="4" class="px-6 py-4 whitespace-nowrap text-center">Tidak ada order terbaru</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                    <div class="mt-4">
                        {{ $orders->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>