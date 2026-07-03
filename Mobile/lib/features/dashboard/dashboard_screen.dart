import 'package:flutter/material.dart';

class DashboardScreen extends StatelessWidget {
  const DashboardScreen({super.key});

  Widget infoCard({
    required IconData icon,
    required String title,
    required String value,
    String? suffix,
    bool small = false,
  }) {
    return Container(
      padding: const EdgeInsets.all(14),

      decoration: BoxDecoration(
        color: const Color(0xffF8F5E9),
        borderRadius: BorderRadius.circular(16),
        border: Border.all(
          color: const Color(0xffDDD7B8),
        ),
        boxShadow: [
          BoxShadow(
            color: Colors.blue.withOpacity(0.08),
            blurRadius: 8,
            offset: const Offset(0, 4),
          ),
        ],
      ),

      child: Column(
        crossAxisAlignment: CrossAxisAlignment.start,
        children: [

          Container(
            padding: const EdgeInsets.all(10),

            decoration: const BoxDecoration(
              color: Color(0xffCFF4A9),
              shape: BoxShape.circle,
            ),

            child: Icon(
              icon,
              color: Colors.black,
              size: small ? 18 : 20,
            ),
          ),

          SizedBox(height: small ? 10 : 14),

          Text(
            title,
            style: TextStyle(
              fontSize: small ? 12 : 13,
              color: Colors.black54,
            ),
          ),

          const Spacer(),

          Row(
            crossAxisAlignment: CrossAxisAlignment.end,
            children: [

              Expanded(
                child: Text(
                  value,
                  overflow: TextOverflow.ellipsis,
                  maxLines: 1,

                  style: TextStyle(
                    fontSize: small ? 16 : 15,
                    fontWeight: FontWeight.bold,
                  ),
                ),
              ),

              if (suffix != null)
                Padding(
                  padding: const EdgeInsets.only(
                    left: 4,
                    bottom: 2,
                  ),

                  child: Text(
                    suffix,
                    style: const TextStyle(
                      fontSize: 11,
                      color: Colors.black54,
                    ),
                  ),
                ),
            ],
          ),
        ],
      ),
    );
  }

  Widget menuItem({
    required IconData icon,
    required String title,
    required String subtitle,
  }) {
    return Container(
      margin: const EdgeInsets.only(bottom: 16),
      padding: const EdgeInsets.all(14),

      decoration: BoxDecoration(
        color: Colors.white,
        borderRadius: BorderRadius.circular(16),
        border: Border.all(
          color: const Color(0xffDDEAF7),
        ),
        boxShadow: [
          BoxShadow(
            color: Colors.blue.withOpacity(0.08),
            blurRadius: 8,
            offset: const Offset(0, 4),
          )
        ],
      ),

      child: Row(
        children: [

          Container(
            padding: const EdgeInsets.all(12),

            decoration: BoxDecoration(
              color: const Color(0xffD9ECFF),
              borderRadius: BorderRadius.circular(12),
            ),

            child: Icon(
              icon,
              color: Colors.black,
              size: 22,
            ),
          ),

          const SizedBox(width: 14),

          Expanded(
            child: Column(
              crossAxisAlignment: CrossAxisAlignment.start,
              children: [

                Text(
                  title,
                  style: const TextStyle(
                    fontWeight: FontWeight.w600,
                    fontSize: 16,
                  ),
                ),

                const SizedBox(height: 4),

                Text(
                  subtitle,
                  style: const TextStyle(
                    color: Colors.grey,
                    fontSize: 11,
                  ),
                ),
              ],
            ),
          ),

          const Icon(
            Icons.arrow_forward_ios_rounded,
            size: 18,
          ),
        ],
      ),
    );
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      backgroundColor: Colors.white,

      body: SafeArea(
        child: SingleChildScrollView(
          padding: const EdgeInsets.symmetric(
            horizontal: 20,
            vertical: 18,
          ),

          child: Column(
            crossAxisAlignment: CrossAxisAlignment.start,
            children: [

              Row(
                mainAxisAlignment: MainAxisAlignment.spaceBetween,
                children: [

                  Image.asset(
                    'assets/logo.png',
                    width: 95,
                    fit: BoxFit.contain,
                  ),

                  const Row(
                    children: [

                      Icon(
                        Icons.notifications_none_outlined,
                        size: 22,
                      ),

                      SizedBox(width: 12),

                      Icon(
                        Icons.person_outline,
                        size: 22,
                      ),
                    ],
                  )
                ],
              ),

              const SizedBox(height: 18),

              const Text(
                'HALO, RASYA APRILIA',
                style: TextStyle(
                  fontSize: 28,
                  fontWeight: FontWeight.w500,
                ),
              ),

              const SizedBox(height: 4),

              const Text(
                'Selamat datang di dashboard monitoring Kalisawah Smart Tourism',
                style: TextStyle(
                  color: Colors.grey,
                  fontSize: 11,
                ),
              ),

              const SizedBox(height: 30),

              Row(
                mainAxisAlignment: MainAxisAlignment.spaceBetween,
                children: [

                  const Text(
                    'Ringkasan Hari ini',
                    style: TextStyle(
                      fontSize: 24,
                      fontWeight: FontWeight.w600,
                    ),
                  ),

                  Row(
                    children: [

                      const Text(
                        '17 Mei 2026',
                        style: TextStyle(
                          fontSize: 11,
                          color: Colors.black54,
                        ),
                      ),

                      const SizedBox(width: 6),

                      const Icon(
                        Icons.calendar_today_outlined,
                        size: 15,
                      ),

                      const SizedBox(width: 4),

                      const Icon(
                        Icons.keyboard_arrow_down,
                        size: 18,
                      ),
                    ],
                  )
                ],
              ),

              const SizedBox(height: 18),

              Row(
                children: [

                  Expanded(
                    child: SizedBox(
                      height: 145,
                      child: infoCard(
                        icon: Icons.payments_outlined,
                        title: 'Pemasukan Hari Ini',
                        value: 'Rp 10.700.000',
                      ),
                    ),
                  ),

                  const SizedBox(width: 14),

                  Expanded(
                    child: SizedBox(
                      height: 145,
                      child: infoCard(
                        icon: Icons.people_alt_outlined,
                        title: 'Pengunjung Hari Ini',
                        value: '200',
                        suffix: 'Orang',
                      ),
                    ),
                  ),
                ],
              ),

              const SizedBox(height: 14),

              Row(
                children: [

                  Expanded(
                    flex: 2,
                    child: SizedBox(
                      height: 145,
                      child: infoCard(
                        icon: Icons.account_balance_wallet_outlined,
                        title: 'Pengeluaran Hari Ini',
                        value: 'Rp 3.000.000',
                      ),
                    ),
                  ),

                  const SizedBox(width: 14),

                  Expanded(
                    child: SizedBox(
                      height: 145,
                      child: infoCard(
                        icon: Icons.calendar_month_outlined,
                        title: 'Booking\nHari Ini',
                        value: '15',
                        small: true,
                      ),
                    ),
                  ),

                  const SizedBox(width: 14),

                  Expanded(
                    child: SizedBox(
                      height: 145,
                      child: infoCard(
                        icon: Icons.inventory_2_outlined,
                        title: 'Inventaris\nKondisi Normal',
                        value: '50',
                        small: true,
                      ),
                    ),
                  ),
                ],
              ),

              const SizedBox(height: 34),

              const Text(
                'Menu Utama',
                style: TextStyle(
                  fontSize: 24,
                  fontWeight: FontWeight.w600,
                ),
              ),

              const SizedBox(height: 18),

              menuItem(
                icon: Icons.file_download_outlined,
                title: 'Laporan Keuangan',
                subtitle:
                    'Lihat pemasukan, pengeluaran, dan riwayat transaksi',
              ),

              menuItem(
                icon: Icons.groups_outlined,
                title: 'Analitik Pengunjung',
                subtitle: 'Lihat data dan statistik pengunjung',
              ),

              menuItem(
                icon: Icons.inventory_2_outlined,
                title: 'Inventaris',
                subtitle: 'Pantau stok dan kondisi inventaris',
              ),

              menuItem(
                icon: Icons.list_alt_outlined,
                title: 'Histori Booking',
                subtitle: 'Lihat semua riwayat pemesanan',
              ),

              const SizedBox(height: 30),

              const Center(
                child: Text(
                  '© Kalisawah Smart Tourism',
                  style: TextStyle(
                    color: Colors.grey,
                    fontSize: 11,
                  ),
                ),
              )
            ],
          ),
        ),
      ),
    );
  }
}