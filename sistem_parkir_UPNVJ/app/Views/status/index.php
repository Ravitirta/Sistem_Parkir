<h2 class="text-2xl font-semibold mb-4 text-gray-800">Status Area Parkir UPNVJ</h2>
<p class="text-sm text-gray-500 mb-6">Menampilkan 7 log status terakhir perbaruan kapasitas parkir.</p>

<div class="overflow-x-auto bg-white shadow-lg rounded-lg">
    <table class="min-w-full divide-y divide-gray-200">
        <thead class="bg-blue-600 text-white">
            <tr>
                <th scope="col" class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider">
                    Nama Area
                </th>
                <th scope="col" class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider">
                    Status Area
                </th>
                <th scope="col" class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider">
                    Jam Update
                </th>
                <th scope="col" class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider">
                    Kapasitas Terisi
                </th>
                <th scope="col" class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider">
                    Kapasitas Maks
                </th>
            </tr>
        </thead>
        <tbody class="bg-white divide-y divide-gray-200">
            <?php if (empty($statusParkir)): ?>
                <tr>
                    <td colspan="5" class="px-6 py-4 whitespace-nowrap text-center text-sm text-gray-500">
                        Tidak ada data status parkir yang tersedia.
                    </td>
                </tr>
            <?php else: ?>
                <?php foreach ($statusParkir as $status): ?>
                    <tr>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                            <?= esc($status['nama_area']) ?>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <?php 
                                $badgeColor = ($status['status_area'] == 'Penuh') ? 'bg-red-100 text-red-800' : 'bg-green-100 text-green-800';
                            ?>
                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full <?= $badgeColor ?>">
                                <?= esc($status['status_area']) ?>
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                            <?= esc($status['jam']) ?>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                            <?= esc($status['kapasitas_now']) ?>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                            <?= esc($status['kapasitas_max']) ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php endif; ?>
        </tbody>
    </table>
</div>

