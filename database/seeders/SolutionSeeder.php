<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class SolutionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $solutions = [
            [
                'title' => 'Network Infrastructure Solutions',
                'icon_class' => 'ri-router-line',
                'summary' => 'Structured cabling, fiber networks, LAN/WAN design, wireless deployment, and network maintenance.',
                'description' => '<h3>Build a Solid Foundation for Your Business</h3><p>We provide comprehensive network infrastructure services designed to ensure seamless connectivity and high performance. Our services include structured cabling for offices, high-speed fiber optic installation, robust LAN/WAN designs, and optimized wireless network deployments.</p>',
                'order' => 1
            ],
            [
                'title' => 'Security & Surveillance Solutions',
                'icon_class' => 'ri-shield-check-line',
                'summary' => 'CCTV, access control, alarm systems, installation, integration, and AMC services.',
                'description' => '<h3>Advanced Security for Your Assets</h3><p>Protect your premises with our state-of-the-art surveillance and security systems. We specialize in high-definition CCTV installations, biometric access control systems, and integrated alarm solutions, all backed by reliable maintenance contracts.</p>',
                'order' => 2
            ],
            [
                'title' => 'Communication & Telephony Solutions',
                'icon_class' => 'ri-phone-find-line',
                'summary' => 'Land phones, IP phones, wired communication systems, and office telephony setup.',
                'description' => '<h3>Seamless Communication Channels</h3><p>Stay connected with your team and clients through our advanced telephony solutions. From traditional landlines to modern IP-PBX systems, we handle the complete setup and integration of enterprise communication networks.</p>',
                'order' => 3
            ],
            [
                'title' => 'Computer Systems & IT Hardware',
                'icon_class' => 'ri-computer-line',
                'summary' => 'Desktops, laptops, servers, peripherals, supply, configuration, and deployment.',
                'description' => '<h3>High-Performance Computing Hardware</h3><p>We supply and deploy a wide range of computer systems tailored to your business needs. This includes high-spec servers, business-grade laptops, and powerful workstations, fully configured for immediate use.</p>',
                'order' => 4
            ],
            [
                'title' => 'IT Support & Maintenance',
                'icon_class' => 'ri-customer-service-2-line',
                'summary' => 'Computer repair, diagnostics, OS installation, data backup, and preventive maintenance.',
                'description' => '<h3>Reliable IT Support When You Need It</h3><p>Ensure your business operations never stop with our expert IT support. We offer professional diagnostics, hardware repairs, OS deployment, and critical data backup solutions to keep your systems running smoothly.</p>',
                'order' => 5
            ],
            [
                'title' => 'Annual Maintenance Contracts (AMC)',
                'icon_class' => 'ri-handshake-line',
                'summary' => 'SLA-based support for IT infrastructure, networks, and security systems.',
                'description' => '<h3>Peace of Mind with Expert AMC Services</h3><p>Our Annual Maintenance Contracts provide guaranteed SLA-based support for your entire IT ecosystem. We take care of regular updates, security patches, and emergency troubleshooting, allowing you to focus on your core business.</p>',
                'order' => 6
            ]
        ];

        foreach ($solutions as $solution) {
            \App\Models\Solution::create($solution);
        }
    }
}
