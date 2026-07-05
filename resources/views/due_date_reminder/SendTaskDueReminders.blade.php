<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reminder Dateline Tugas</title>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700&display=swap');

        body,
        table,
        td,
        a {
            -webkit-text-size-adjust: 100%;
            -ms-text-size-adjust: 100%;
        }

        table,
        td {
            mso-table-lspace: 0pt;
            mso-table-rspace: 0pt;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            background-color: #F0F2F5;
            font-family: 'Plus Jakarta Sans', -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif;
            margin: 0;
            padding: 0;
            width: 100% !important;
        }

        .badge {
            display: inline-block;
            font-size: 12px;
            font-weight: 600;
            border-radius: 6px;
            padding: 3px 10px;
        }

        .badge-high {
            background: #FEE2E2;
            color: #B91C1C;
        }

        .badge-medium {
            background: #FEF3C7;
            color: #92400E;
        }

        .badge-low {
            background: #D1FAE5;
            color: #065F46;
        }

        .badge-default {
            background: #F3F4F6;
            color: #374151;
        }

        @media screen and (max-width: 600px) {
            .outer-td {
                padding: 20px 10px !important;
            }

            .card-hero {
                padding: 24px 20px 28px !important;
            }

            .hero-title {
                font-size: 22px !important;
            }

            .card-body {
                padding: 20px 16px !important;
            }

            .task-box {
                padding: 16px 14px !important;
            }

            /* Stack countdown cells on small screens */
            .cd-num-td,
            .cd-label-td {
                display: block !important;
                width: 100% !important;
            }

            .cd-num-td {
                padding: 14px 16px 4px !important;
                text-align: center !important;
            }

            .cd-label-td {
                padding: 4px 16px 14px !important;
                text-align: center !important;
            }

            /* Stack task detail rows on small screens */
            .tr-label-td,
            .tr-colon-td,
            .tr-value-td {
                display: block !important;
                width: 100% !important;
            }

            .tr-colon-td {
                display: none !important;
            }

            .tr-label-td {
                padding: 8px 0 2px !important;
            }

            .tr-value-td {
                padding: 0 0 8px !important;
            }
        }
    </style>
</head>

<body>
    <!-- Outer wrapper table -->
    <table width="100%" cellpadding="0" cellspacing="0" role="presentation" style="background-color:#F0F2F5;">
        <tr>
            <td class="outer-td" align="center" valign="top" style="padding:40px 16px;">

                <!-- Inner 600px container -->
                <table width="100%" cellpadding="0" cellspacing="0" role="presentation" style="max-width:600px;">

                    <!-- Card -->
                    <tr>
                        <td style="background:#FFFFFF;border-radius:20px;overflow:hidden;">

                            <!-- Hero -->
                            <table width="100%" cellpadding="0" cellspacing="0" role="presentation">
                                <tr>
                                    <td class="card-hero" align="left" valign="top"
                                        style="background:linear-gradient(135deg,#92400E 0%,#B45309 50%,#D97706 100%);padding:40px 40px 48px;border-radius:20px 20px 0 0;">

                                        <!-- Badge -->
                                        <table cellpadding="0" cellspacing="0" role="presentation"
                                            style="margin-bottom:20px;">
                                            <tr>
                                                <td
                                                    style="background:rgba(255,255,255,0.15);border:1px solid rgba(255,255,255,0.2);border-radius:100px;padding:6px 14px;">
                                                    <table cellpadding="0" cellspacing="0" role="presentation">
                                                        <tr>
                                                            <td valign="middle" style="padding-right:6px;">
                                                                <div
                                                                    style="width:6px;height:6px;border-radius:50%;background:#FCD34D;">
                                                                </div>
                                                            </td>
                                                            <td valign="middle">
                                                                <span
                                                                    style="font-size:12px;font-weight:600;color:rgba(255,255,255,0.9);letter-spacing:0.5px;text-transform:uppercase;font-family:'Plus Jakarta Sans',sans-serif;">Reminder
                                                                    Dateline</span>
                                                            </td>
                                                        </tr>
                                                    </table>
                                                </td>
                                            </tr>
                                        </table>

                                        <div class="hero-title"
                                            style="font-size:30px;font-weight:700;color:#FFFFFF;line-height:1.25;letter-spacing:-0.5px;margin-bottom:10px;font-family:'Plus Jakarta Sans',sans-serif;">
                                            Tugas Jatuh Tempo<br>Hari Ini!
                                        </div>
                                        <div
                                            style="font-size:15px;color:rgba(255,255,255,0.75);line-height:1.6;font-family:'Plus Jakarta Sans',sans-serif;">
                                            Segera selesaikan tugasmu sebelum waktu habis.
                                        </div>
                                    </td>
                                </tr>
                            </table>

                            <!-- Card body -->
                            <table width="100%" cellpadding="0" cellspacing="0" role="presentation">
                                <tr>
                                    <td class="card-body" style="padding:36px 40px;">

                                        <!-- Greeting -->
                                        <p
                                            style="font-size:16px;color:#374151;line-height:1.7;margin-bottom:28px;font-family:'Plus Jakarta Sans',sans-serif;">
                                            Halo, <strong style="color:#111827;">{{ $assignee->name }}</strong>!<br><br>
                                            Ini adalah pengingat bahwa kamu memiliki tugas yang akan jatuh tempo
                                            <strong style="color:#111827;">hari ini</strong>.
                                            Pastikan tugas diselesaikan sebelum dateline berakhir.
                                        </p>

                                        <!-- Countdown -->
                                        <table width="100%" cellpadding="0" cellspacing="0" role="presentation"
                                            style="background:#FFF7ED;border:1px solid #FDBA74;border-radius:12px;margin-bottom:28px;">
                                            <tr>
                                                <td class="cd-num-td" valign="middle"
                                                    style="padding:16px 0 16px 20px;width:125px;">
                                                    <span
                                                        style="font-size:40px;font-weight:700;color:#EA580C;line-height:1;font-family:'Plus Jakarta Sans',sans-serif;">{{ $hoursLeft }}
                                                        jam</span>
                                                </td>
                                                <td class="cd-label-td" valign="middle"
                                                    style="padding:16px 20px 16px 14px;">
                                                    <span
                                                        style="font-size:13px;color:#7C2D12;line-height:1.6;font-family:'Plus Jakarta Sans',sans-serif;">
                                                        <strong style="color:#9A3412;">sebelum hari
                                                            berganti.</strong><br>
                                                        Dateline: <strong
                                                            style="color:#9A3412;">{{ $dueDate }}</strong>
                                                    </span>
                                                </td>
                                            </tr>
                                        </table>

                                        <!-- Task detail box -->
                                        <table width="100%" cellpadding="0" cellspacing="0" role="presentation"
                                            style="background:#FFFBEB;border:1px solid #FDE68A;border-radius:14px;margin-bottom:28px;">
                                            <tr>
                                                <td class="task-box" style="padding:24px 28px;">

                                                    <!-- Box header -->
                                                    <table width="100%" cellpadding="0" cellspacing="0"
                                                        role="presentation"
                                                        style="margin-bottom:18px;padding-bottom:16px;border-bottom:1px solid #FDE68A;">
                                                        <tr>
                                                            <td valign="middle" style="width:16px;padding-right:6px;">
                                                                <div
                                                                    style="width:6px;height:6px;border-radius:50%;background:#92400E;">
                                                                </div>
                                                            </td>
                                                            <td valign="middle" style="padding-left:10px;">
                                                                <span
                                                                    style="font-size:13px;font-weight:700;color:#92400E;letter-spacing:0.3px;text-transform:uppercase;font-family:'Plus Jakarta Sans',sans-serif;">Detail
                                                                    Tugas</span>
                                                            </td>
                                                        </tr>
                                                    </table>

                                                    <!-- Detail rows -->
                                                    <table width="100%" cellpadding="0" cellspacing="0"
                                                        role="presentation">

                                                        <!-- Nama tugas -->
                                                        <tr style="border-bottom:1px solid #FEF3C7;">
                                                            <td class="tr-label-td" valign="top"
                                                                style="font-size:13px;color:#78350F;font-weight:500;padding:9px 0;width:38%;font-family:'Plus Jakarta Sans',sans-serif;">
                                                                Nama Tugas
                                                            </td>
                                                            <td class="tr-colon-td" valign="top"
                                                                style="font-size:13px;color:#78350F;font-weight:500;padding:9px 4px;width:14px;text-align:center;">
                                                                :
                                                            </td>
                                                            <td class="tr-value-td" valign="top"
                                                                style="font-size:14px;color:#111827;font-weight:600;padding:9px 0 9px 8px;font-family:'Plus Jakarta Sans',sans-serif;">
                                                                {{ $task->title }}
                                                            </td>
                                                        </tr>

                                                        <!-- Dateline -->
                                                        <tr style="border-bottom:1px solid #FEF3C7;">
                                                            <td class="tr-label-td" valign="top"
                                                                style="font-size:13px;color:#78350F;font-weight:500;padding:9px 0;width:38%;font-family:'Plus Jakarta Sans',sans-serif;">
                                                                Dateline
                                                            </td>
                                                            <td class="tr-colon-td" valign="top"
                                                                style="font-size:13px;color:#78350F;font-weight:500;padding:9px 4px;width:14px;text-align:center;">
                                                                :
                                                            </td>
                                                            <td class="tr-value-td" valign="top"
                                                                style="font-size:14px;color:#111827;font-weight:600;padding:9px 0 9px 8px;font-family:'Plus Jakarta Sans',sans-serif;">
                                                                {{ $dueDate }}
                                                            </td>
                                                        </tr>

                                                        @if ($bucketName)
                                                            <!-- Proyek -->
                                                            <tr style="border-bottom:1px solid #FEF3C7;">
                                                                <td class="tr-label-td" valign="top"
                                                                    style="font-size:13px;color:#78350F;font-weight:500;padding:9px 0;width:38%;font-family:'Plus Jakarta Sans',sans-serif;">
                                                                    Proyek
                                                                </td>
                                                                <td class="tr-colon-td" valign="top"
                                                                    style="font-size:13px;color:#78350F;font-weight:500;padding:9px 4px;width:14px;text-align:center;">
                                                                    :
                                                                </td>
                                                                <td class="tr-value-td" valign="top"
                                                                    style="font-size:14px;color:#111827;font-weight:600;padding:9px 0 9px 8px;font-family:'Plus Jakarta Sans',sans-serif;">
                                                                    {{ $bucketName }}
                                                                </td>
                                                            </tr>
                                                        @endif

                                                        @if ($priority)
                                                            @php
                                                                $badgeClass = match (strtolower($priority)) {
                                                                    'tinggi', 'high' => 'badge-high',
                                                                    'sedang', 'medium' => 'badge-medium',
                                                                    'rendah', 'low' => 'badge-low',
                                                                    default => 'badge-default',
                                                                };
                                                                $priorityLabel = match (strtolower($priority)) {
                                                                    'tinggi', 'high' => 'Tinggi',
                                                                    'sedang', 'medium' => 'Sedang',
                                                                    'rendah', 'low' => 'Rendah',
                                                                    default => ucfirst($priority),
                                                                };
                                                            @endphp
                                                            <!-- Prioritas -->
                                                            <tr
                                                                style="{{ $task->description ? 'border-bottom:1px solid #FEF3C7;' : '' }}">
                                                                <td class="tr-label-td" valign="top"
                                                                    style="font-size:13px;color:#78350F;font-weight:500;padding:9px 0;width:38%;font-family:'Plus Jakarta Sans',sans-serif;">
                                                                    Prioritas
                                                                </td>
                                                                <td class="tr-colon-td" valign="top"
                                                                    style="font-size:13px;color:#78350F;font-weight:500;padding:9px 4px;width:14px;text-align:center;">
                                                                    :
                                                                </td>
                                                                <td class="tr-value-td" valign="top"
                                                                    style="padding:9px 0 9px 8px;">
                                                                    <span
                                                                        class="badge {{ $badgeClass }}">{{ $priorityLabel }}</span>
                                                                </td>
                                                            </tr>
                                                        @endif

                                                        @if ($task->description)
                                                            <!-- Deskripsi -->
                                                            <tr>
                                                                <td class="tr-label-td" valign="top"
                                                                    style="font-size:13px;color:#78350F;font-weight:500;padding:9px 0;width:38%;font-family:'Plus Jakarta Sans',sans-serif;">
                                                                    Deskripsi
                                                                </td>
                                                                <td class="tr-colon-td" valign="top"
                                                                    style="font-size:13px;color:#78350F;font-weight:500;padding:9px 4px;width:14px;text-align:center;">
                                                                    :
                                                                </td>
                                                                <td class="tr-value-td" valign="top"
                                                                    style="font-size:13px;color:#374151;font-weight:400;padding:9px 0 9px 8px;line-height:1.6;font-family:'Plus Jakarta Sans',sans-serif;">
                                                                    {{ Str::limit($task->description, 120) }}
                                                                </td>
                                                            </tr>
                                                        @endif

                                                    </table>
                                                </td>
                                            </tr>
                                        </table>

                                        <!-- Divider -->
                                        <table width="100%" cellpadding="0" cellspacing="0" role="presentation"
                                            style="margin-bottom:28px;">
                                            <tr>
                                                <td style="height:1px;background:#F3F4F6;font-size:0;line-height:0;">
                                                    &nbsp;</td>
                                            </tr>
                                        </table>

                                        <!-- Signature -->
                                        <p
                                            style="font-size:14px;color:#6B7280;line-height:1.7;font-family:'Plus Jakarta Sans',sans-serif;">
                                            Salam,<br>
                                            <strong style="color:#111827;">Tim Staflo</strong>
                                        </p>

                                    </td>
                                </tr>
                            </table>

                        </td>
                    </tr>

                    <!-- Footer -->
                    <tr>
                        <td align="center" style="padding-top:28px;">
                            <p
                                style="font-size:12px;color:#9CA3AF;line-height:1.7;font-family:'Plus Jakarta Sans',sans-serif;">
                                Email ini dikirim secara otomatis oleh sistem Staflo.<br>
                                Harap tidak membalas email ini.
                            </p>
                        </td>
                    </tr>

                </table>
            </td>
        </tr>
    </table>
</body>

</html>
