<?php

namespace Database\Seeders;

use App\Models\DocumentTemplate;
use Illuminate\Database\Seeder;

class DocumentTemplatesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // قوالب التقارير الفنية
        $this->createTechnicalReportTemplates();
        
        // قوالب عروض الأسعار
        $this->createQuotationTemplates();
        
        $this->command->info('تم إنشاء قوالب المستندات بنجاح!');
    }

    /**
     * إنشاء قوالب التقارير الفنية
     */
    private function createTechnicalReportTemplates(): void
    {
        // قالب تقرير فني شامل
        DocumentTemplate::firstOrCreate(
            ['name' => 'تقرير فني شامل'],
            [
                'type' => 'technical_report',
                'content' => $this->getComprehensiveTechnicalReportTemplate(),
                'variables' => ['client_name', 'project_name', 'service_name', 'date'],
                'is_active' => true,
                'order' => 1,
            ]
        );

        // قالب تقرير فني مختصر
        DocumentTemplate::firstOrCreate(
            ['name' => 'تقرير فني مختصر'],
            [
                'type' => 'technical_report',
                'content' => $this->getBriefTechnicalReportTemplate(),
                'variables' => ['client_name', 'project_name', 'date'],
                'is_active' => true,
                'order' => 2,
            ]
        );

        // قالب تقرير فني للمشاريع السكنية
        DocumentTemplate::firstOrCreate(
            ['name' => 'تقرير فني - مشروع سكني'],
            [
                'type' => 'technical_report',
                'content' => $this->getResidentialProjectTemplate(),
                'variables' => ['client_name', 'project_name', 'service_name', 'date'],
                'is_active' => true,
                'order' => 3,
            ]
        );

        // قالب تقرير فني للمشاريع التجارية
        DocumentTemplate::firstOrCreate(
            ['name' => 'تقرير فني - مشروع تجاري'],
            [
                'type' => 'technical_report',
                'content' => $this->getCommercialProjectTemplate(),
                'variables' => ['client_name', 'project_name', 'service_name', 'date'],
                'is_active' => true,
                'order' => 4,
            ]
        );
    }

    /**
     * إنشاء قوالب عروض الأسعار
     */
    private function createQuotationTemplates(): void
    {
        // قالب عرض سعر شامل
        DocumentTemplate::firstOrCreate(
            ['name' => 'عرض سعر شامل'],
            [
                'type' => 'quotation',
                'content' => $this->getComprehensiveQuotationTemplate(),
                'variables' => ['client_name', 'project_name', 'service_name', 'date', 'total_price'],
                'is_active' => true,
                'order' => 1,
            ]
        );

        // قالب عرض سعر مختصر
        DocumentTemplate::firstOrCreate(
            ['name' => 'عرض سعر مختصر'],
            [
                'type' => 'quotation',
                'content' => $this->getBriefQuotationTemplate(),
                'variables' => ['client_name', 'project_name', 'total_price', 'date'],
                'is_active' => true,
                'order' => 2,
            ]
        );

        // قالب عرض سعر للتصميم
        DocumentTemplate::firstOrCreate(
            ['name' => 'عرض سعر - خدمات التصميم'],
            [
                'type' => 'quotation',
                'content' => $this->getDesignQuotationTemplate(),
                'variables' => ['client_name', 'project_name', 'service_name', 'total_price', 'date'],
                'is_active' => true,
                'order' => 3,
            ]
        );

        // قالب عرض سعر للإشراف
        DocumentTemplate::firstOrCreate(
            ['name' => 'عرض سعر - خدمات الإشراف'],
            [
                'type' => 'quotation',
                'content' => $this->getSupervisionQuotationTemplate(),
                'variables' => ['client_name', 'project_name', 'service_name', 'total_price', 'date'],
                'is_active' => true,
                'order' => 4,
            ]
        );
    }

    /**
     * قالب تقرير فني شامل
     */
    private function getComprehensiveTechnicalReportTemplate(): string
    {
        return '<div style="direction: rtl; text-align: right; font-family: Arial, sans-serif; padding: 40px; line-height: 1.8;">
            <div style="text-align: center; margin-bottom: 40px; border-bottom: 3px solid #1db8f8; padding-bottom: 20px;">
                <h1 style="color: #1db8f8; font-size: 32px; margin: 0 0 10px 0;">تقرير فني شامل</h1>
                <p style="color: #666; font-size: 16px; margin: 0;">@{{project_name}}</p>
            </div>

            <div style="margin-bottom: 30px;">
                <h2 style="color: #333; font-size: 24px; border-bottom: 2px solid #1db8f8; padding-bottom: 10px; margin-bottom: 20px;">معلومات المشروع</h2>
                <table style="width: 100%; border-collapse: collapse; margin-bottom: 20px;">
                    <tr>
                        <td style="padding: 10px; background: #f5f5f5; font-weight: bold; width: 30%;">اسم العميل:</td>
                        <td style="padding: 10px;">@{{client_name}}</td>
                    </tr>
                    <tr>
                        <td style="padding: 10px; background: #f5f5f5; font-weight: bold;">اسم المشروع:</td>
                        <td style="padding: 10px;">@{{project_name}}</td>
                    </tr>
                    <tr>
                        <td style="padding: 10px; background: #f5f5f5; font-weight: bold;">نوع الخدمة:</td>
                        <td style="padding: 10px;">@{{service_name}}</td>
                    </tr>
                    <tr>
                        <td style="padding: 10px; background: #f5f5f5; font-weight: bold;">تاريخ التقرير:</td>
                        <td style="padding: 10px;">@{{date}}</td>
                    </tr>
                </table>
            </div>

            <div style="margin-bottom: 30px;">
                <h2 style="color: #333; font-size: 24px; border-bottom: 2px solid #1db8f8; padding-bottom: 10px; margin-bottom: 20px;">الوصف العام</h2>
                <p style="text-align: justify; color: #555; font-size: 16px; line-height: 1.8;">
                    يقدم هذا التقرير الفني تحليلاً شاملاً للمشروع المذكور أعلاه، ويتضمن دراسة تفصيلية للجوانب الفنية والهندسية المتعلقة بالمشروع.
                </p>
            </div>

            <div style="margin-bottom: 30px;">
                <h2 style="color: #333; font-size: 24px; border-bottom: 2px solid #1db8f8; padding-bottom: 10px; margin-bottom: 20px;">الدراسة الفنية</h2>
                <h3 style="color: #1db8f8; font-size: 20px; margin-top: 20px; margin-bottom: 10px;">1. الموقع والمساحة</h3>
                <p style="text-align: justify; color: #555; font-size: 16px; line-height: 1.8;">
                    [يرجى إضافة تفاصيل الموقع والمساحة]
                </p>

                <h3 style="color: #1db8f8; font-size: 20px; margin-top: 20px; margin-bottom: 10px;">2. التصميم المعماري</h3>
                <p style="text-align: justify; color: #555; font-size: 16px; line-height: 1.8;">
                    [يرجى إضافة تفاصيل التصميم المعماري]
                </p>

                <h3 style="color: #1db8f8; font-size: 20px; margin-top: 20px; margin-bottom: 10px;">3. التصميم الإنشائي</h3>
                <p style="text-align: justify; color: #555; font-size: 16px; line-height: 1.8;">
                    [يرجى إضافة تفاصيل التصميم الإنشائي]
                </p>

                <h3 style="color: #1db8f8; font-size: 20px; margin-top: 20px; margin-bottom: 10px;">4. التصميم الكهربائي</h3>
                <p style="text-align: justify; color: #555; font-size: 16px; line-height: 1.8;">
                    [يرجى إضافة تفاصيل التصميم الكهربائي]
                </p>

                <h3 style="color: #1db8f8; font-size: 20px; margin-top: 20px; margin-bottom: 10px;">5. التصميم الميكانيكي</h3>
                <p style="text-align: justify; color: #555; font-size: 16px; line-height: 1.8;">
                    [يرجى إضافة تفاصيل التصميم الميكانيكي]
                </p>
            </div>

            <div style="margin-bottom: 30px;">
                <h2 style="color: #333; font-size: 24px; border-bottom: 2px solid #1db8f8; padding-bottom: 10px; margin-bottom: 20px;">الخلاصة والتوصيات</h2>
                <p style="text-align: justify; color: #555; font-size: 16px; line-height: 1.8;">
                    بناءً على الدراسة الفنية المذكورة أعلاه، نوصي بما يلي:
                </p>
                <ul style="color: #555; font-size: 16px; line-height: 1.8;">
                    <li>[التوصية الأولى]</li>
                    <li>[التوصية الثانية]</li>
                    <li>[التوصية الثالثة]</li>
                </ul>
            </div>

            <div style="margin-top: 50px; text-align: left; border-top: 2px solid #1db8f8; padding-top: 20px;">
                <p style="color: #666; font-size: 14px; margin: 5px 0;">تم إعداد هذا التقرير بواسطة:</p>
                <p style="color: #333; font-size: 16px; font-weight: bold; margin: 5px 0;">مكتب المنار للاستشارات الهندسية</p>
                <p style="color: #666; font-size: 14px; margin: 5px 0;">تاريخ: @{{date}}</p>
            </div>
        </div>';
    }

    /**
     * قالب تقرير فني مختصر
     */
    private function getBriefTechnicalReportTemplate(): string
    {
        return '<div style="direction: rtl; text-align: right; font-family: Arial, sans-serif; padding: 40px; line-height: 1.8;">
            <div style="text-align: center; margin-bottom: 30px;">
                <h1 style="color: #1db8f8; font-size: 28px; margin: 0;">تقرير فني</h1>
                <p style="color: #666; font-size: 14px; margin: 10px 0;">@{{project_name}}</p>
            </div>

            <div style="margin-bottom: 20px;">
                <p><strong>العميل:</strong> @{{client_name}}</p>
                <p><strong>المشروع:</strong> @{{project_name}}</p>
                <p><strong>التاريخ:</strong> @{{date}}</p>
            </div>

            <div style="margin-bottom: 20px;">
                <h2 style="color: #333; font-size: 20px; border-bottom: 2px solid #1db8f8; padding-bottom: 5px;">الوصف</h2>
                <p style="text-align: justify; color: #555;">[يرجى إضافة وصف المشروع]</p>
            </div>

            <div style="margin-bottom: 20px;">
                <h2 style="color: #333; font-size: 20px; border-bottom: 2px solid #1db8f8; padding-bottom: 5px;">الخلاصة</h2>
                <p style="text-align: justify; color: #555;">[يرجى إضافة الخلاصة]</p>
            </div>
        </div>';
    }

    /**
     * قالب تقرير فني للمشاريع السكنية
     */
    private function getResidentialProjectTemplate(): string
    {
        return '<div style="direction: rtl; text-align: right; font-family: Arial, sans-serif; padding: 40px; line-height: 1.8;">
            <div style="text-align: center; margin-bottom: 40px; border-bottom: 3px solid #1db8f8; padding-bottom: 20px;">
                <h1 style="color: #1db8f8; font-size: 32px; margin: 0 0 10px 0;">تقرير فني - مشروع سكني</h1>
                <p style="color: #666; font-size: 16px; margin: 0;">@{{project_name}}</p>
            </div>

            <div style="margin-bottom: 30px;">
                <h2 style="color: #333; font-size: 24px; border-bottom: 2px solid #1db8f8; padding-bottom: 10px; margin-bottom: 20px;">معلومات المشروع</h2>
                <table style="width: 100%; border-collapse: collapse;">
                    <tr>
                        <td style="padding: 10px; background: #f5f5f5; font-weight: bold; width: 30%;">اسم العميل:</td>
                        <td style="padding: 10px;">@{{client_name}}</td>
                    </tr>
                    <tr>
                        <td style="padding: 10px; background: #f5f5f5; font-weight: bold;">اسم المشروع:</td>
                        <td style="padding: 10px;">@{{project_name}}</td>
                    </tr>
                    <tr>
                        <td style="padding: 10px; background: #f5f5f5; font-weight: bold;">نوع الخدمة:</td>
                        <td style="padding: 10px;">@{{service_name}}</td>
                    </tr>
                    <tr>
                        <td style="padding: 10px; background: #f5f5f5; font-weight: bold;">تاريخ التقرير:</td>
                        <td style="padding: 10px;">@{{date}}</td>
                    </tr>
                </table>
            </div>

            <div style="margin-bottom: 30px;">
                <h2 style="color: #333; font-size: 24px; border-bottom: 2px solid #1db8f8; padding-bottom: 10px; margin-bottom: 20px;">مواصفات المشروع السكني</h2>
                <h3 style="color: #1db8f8; font-size: 20px; margin-top: 20px; margin-bottom: 10px;">1. المساحة والموقع</h3>
                <p style="text-align: justify; color: #555; font-size: 16px;">[تفاصيل المساحة والموقع]</p>

                <h3 style="color: #1db8f8; font-size: 20px; margin-top: 20px; margin-bottom: 10px;">2. التصميم المعماري</h3>
                <p style="text-align: justify; color: #555; font-size: 16px;">[تفاصيل التصميم المعماري للمشروع السكني]</p>

                <h3 style="color: #1db8f8; font-size: 20px; margin-top: 20px; margin-bottom: 10px;">3. عدد الوحدات السكنية</h3>
                <p style="text-align: justify; color: #555; font-size: 16px;">[عدد الوحدات السكنية وتوزيعها]</p>

                <h3 style="color: #1db8f8; font-size: 20px; margin-top: 20px; margin-bottom: 10px;">4. المرافق والخدمات</h3>
                <p style="text-align: justify; color: #555; font-size: 16px;">[المرافق والخدمات المتوفرة]</p>
            </div>

            <div style="margin-bottom: 30px;">
                <h2 style="color: #333; font-size: 24px; border-bottom: 2px solid #1db8f8; padding-bottom: 10px; margin-bottom: 20px;">التوصيات</h2>
                <p style="text-align: justify; color: #555; font-size: 16px;">[التوصيات الخاصة بالمشروع السكني]</p>
            </div>
        </div>';
    }

    /**
     * قالب تقرير فني للمشاريع التجارية
     */
    private function getCommercialProjectTemplate(): string
    {
        return '<div style="direction: rtl; text-align: right; font-family: Arial, sans-serif; padding: 40px; line-height: 1.8;">
            <div style="text-align: center; margin-bottom: 40px; border-bottom: 3px solid #1db8f8; padding-bottom: 20px;">
                <h1 style="color: #1db8f8; font-size: 32px; margin: 0 0 10px 0;">تقرير فني - مشروع تجاري</h1>
                <p style="color: #666; font-size: 16px; margin: 0;">@{{project_name}}</p>
            </div>

            <div style="margin-bottom: 30px;">
                <h2 style="color: #333; font-size: 24px; border-bottom: 2px solid #1db8f8; padding-bottom: 10px; margin-bottom: 20px;">معلومات المشروع</h2>
                <table style="width: 100%; border-collapse: collapse;">
                    <tr>
                        <td style="padding: 10px; background: #f5f5f5; font-weight: bold; width: 30%;">اسم العميل:</td>
                        <td style="padding: 10px;">@{{client_name}}</td>
                    </tr>
                    <tr>
                        <td style="padding: 10px; background: #f5f5f5; font-weight: bold;">اسم المشروع:</td>
                        <td style="padding: 10px;">@{{project_name}}</td>
                    </tr>
                    <tr>
                        <td style="padding: 10px; background: #f5f5f5; font-weight: bold;">نوع الخدمة:</td>
                        <td style="padding: 10px;">@{{service_name}}</td>
                    </tr>
                    <tr>
                        <td style="padding: 10px; background: #f5f5f5; font-weight: bold;">تاريخ التقرير:</td>
                        <td style="padding: 10px;">@{{date}}</td>
                    </tr>
                </table>
            </div>

            <div style="margin-bottom: 30px;">
                <h2 style="color: #333; font-size: 24px; border-bottom: 2px solid #1db8f8; padding-bottom: 10px; margin-bottom: 20px;">مواصفات المشروع التجاري</h2>
                <h3 style="color: #1db8f8; font-size: 20px; margin-top: 20px; margin-bottom: 10px;">1. الموقع والمساحة</h3>
                <p style="text-align: justify; color: #555; font-size: 16px;">[تفاصيل الموقع والمساحة للمشروع التجاري]</p>

                <h3 style="color: #1db8f8; font-size: 20px; margin-top: 20px; margin-bottom: 10px;">2. الاستخدام التجاري</h3>
                <p style="text-align: justify; color: #555; font-size: 16px;">[نوع الاستخدام التجاري]</p>

                <h3 style="color: #1db8f8; font-size: 20px; margin-top: 20px; margin-bottom: 10px;">3. التصميم والمواصفات</h3>
                <p style="text-align: justify; color: #555; font-size: 16px;">[التصميم والمواصفات الخاصة بالمشروع التجاري]</p>

                <h3 style="color: #1db8f8; font-size: 20px; margin-top: 20px; margin-bottom: 10px;">4. المتطلبات الخاصة</h3>
                <p style="text-align: justify; color: #555; font-size: 16px;">[المتطلبات الخاصة بالمشاريع التجارية]</p>
            </div>

            <div style="margin-bottom: 30px;">
                <h2 style="color: #333; font-size: 24px; border-bottom: 2px solid #1db8f8; padding-bottom: 10px; margin-bottom: 20px;">التوصيات</h2>
                <p style="text-align: justify; color: #555; font-size: 16px;">[التوصيات الخاصة بالمشروع التجاري]</p>
            </div>
        </div>';
    }

    /**
     * قالب عرض سعر شامل
     */
    private function getComprehensiveQuotationTemplate(): string
    {
        return '<div style="direction: rtl; text-align: right; font-family: Arial, sans-serif; padding: 40px; line-height: 1.8;">
            <div style="text-align: center; margin-bottom: 40px; border-bottom: 3px solid #1db8f8; padding-bottom: 20px;">
                <h1 style="color: #1db8f8; font-size: 32px; margin: 0 0 10px 0;">عرض سعر شامل</h1>
                <p style="color: #666; font-size: 16px; margin: 0;">@{{project_name}}</p>
            </div>

            <div style="margin-bottom: 30px;">
                <h2 style="color: #333; font-size: 24px; border-bottom: 2px solid #1db8f8; padding-bottom: 10px; margin-bottom: 20px;">معلومات العميل والمشروع</h2>
                <table style="width: 100%; border-collapse: collapse; margin-bottom: 20px;">
                    <tr>
                        <td style="padding: 10px; background: #f5f5f5; font-weight: bold; width: 30%;">اسم العميل:</td>
                        <td style="padding: 10px;">@{{client_name}}</td>
                    </tr>
                    <tr>
                        <td style="padding: 10px; background: #f5f5f5; font-weight: bold;">اسم المشروع:</td>
                        <td style="padding: 10px;">@{{project_name}}</td>
                    </tr>
                    <tr>
                        <td style="padding: 10px; background: #f5f5f5; font-weight: bold;">نوع الخدمة:</td>
                        <td style="padding: 10px;">@{{service_name}}</td>
                    </tr>
                    <tr>
                        <td style="padding: 10px; background: #f5f5f5; font-weight: bold;">تاريخ العرض:</td>
                        <td style="padding: 10px;">@{{date}}</td>
                    </tr>
                </table>
            </div>

            <div style="margin-bottom: 30px;">
                <h2 style="color: #333; font-size: 24px; border-bottom: 2px solid #1db8f8; padding-bottom: 10px; margin-bottom: 20px;">تفاصيل الخدمات</h2>
                <table style="width: 100%; border-collapse: collapse; margin-bottom: 20px;">
                    <thead>
                        <tr style="background: #1db8f8; color: white;">
                            <th style="padding: 12px; text-align: right; border: 1px solid #ddd;">الخدمة</th>
                            <th style="padding: 12px; text-align: center; border: 1px solid #ddd;">الكمية</th>
                            <th style="padding: 12px; text-align: center; border: 1px solid #ddd;">السعر</th>
                            <th style="padding: 12px; text-align: center; border: 1px solid #ddd;">الإجمالي</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td style="padding: 10px; border: 1px solid #ddd;">[اسم الخدمة الأولى]</td>
                            <td style="padding: 10px; text-align: center; border: 1px solid #ddd;">[الكمية]</td>
                            <td style="padding: 10px; text-align: center; border: 1px solid #ddd;">[السعر]</td>
                            <td style="padding: 10px; text-align: center; border: 1px solid #ddd;">[الإجمالي]</td>
                        </tr>
                        <tr>
                            <td style="padding: 10px; border: 1px solid #ddd;">[اسم الخدمة الثانية]</td>
                            <td style="padding: 10px; text-align: center; border: 1px solid #ddd;">[الكمية]</td>
                            <td style="padding: 10px; text-align: center; border: 1px solid #ddd;">[السعر]</td>
                            <td style="padding: 10px; text-align: center; border: 1px solid #ddd;">[الإجمالي]</td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <div style="margin-bottom: 30px; text-align: left;">
                <table style="width: 50%; margin-left: auto; border-collapse: collapse;">
                    <tr>
                        <td style="padding: 12px; background: #f5f5f5; font-weight: bold; font-size: 18px; border: 1px solid #ddd;">الإجمالي:</td>
                        <td style="padding: 12px; font-size: 18px; font-weight: bold; color: #1db8f8; border: 1px solid #ddd;">@{{total_price}} ريال</td>
                    </tr>
                </table>
            </div>

            <div style="margin-bottom: 30px;">
                <h2 style="color: #333; font-size: 24px; border-bottom: 2px solid #1db8f8; padding-bottom: 10px; margin-bottom: 20px;">شروط وأحكام</h2>
                <ul style="color: #555; font-size: 16px; line-height: 1.8;">
                    <li>هذا العرض ساري لمدة [عدد الأيام] يوم من تاريخه</li>
                    <li>السعر يشمل جميع الخدمات المذكورة أعلاه</li>
                    <li>يتم الدفع حسب الاتفاق المبرم</li>
                    <li>جميع الأسعار خاضعة للتغيير حسب طبيعة المشروع</li>
                </ul>
            </div>

            <div style="margin-top: 50px; text-align: left; border-top: 2px solid #1db8f8; padding-top: 20px;">
                <p style="color: #666; font-size: 14px; margin: 5px 0;">تم إعداد هذا العرض بواسطة:</p>
                <p style="color: #333; font-size: 16px; font-weight: bold; margin: 5px 0;">مكتب المنار للاستشارات الهندسية</p>
                <p style="color: #666; font-size: 14px; margin: 5px 0;">تاريخ: @{{date}}</p>
            </div>
        </div>';
    }

    /**
     * قالب عرض سعر مختصر
     */
    private function getBriefQuotationTemplate(): string
    {
        return '<div style="direction: rtl; text-align: right; font-family: Arial, sans-serif; padding: 40px; line-height: 1.8;">
            <div style="text-align: center; margin-bottom: 30px;">
                <h1 style="color: #1db8f8; font-size: 28px; margin: 0;">عرض سعر</h1>
                <p style="color: #666; font-size: 14px; margin: 10px 0;">@{{project_name}}</p>
            </div>

            <div style="margin-bottom: 20px;">
                <p><strong>العميل:</strong> @{{client_name}}</p>
                <p><strong>المشروع:</strong> @{{project_name}}</p>
                <p><strong>التاريخ:</strong> @{{date}}</p>
            </div>

            <div style="margin-bottom: 20px;">
                <h2 style="color: #333; font-size: 20px; border-bottom: 2px solid #1db8f8; padding-bottom: 5px;">الخدمات المقدمة</h2>
                <p style="text-align: justify; color: #555;">[قائمة الخدمات]</p>
            </div>

            <div style="margin-bottom: 20px; text-align: left;">
                <p style="font-size: 20px; font-weight: bold; color: #1db8f8;">السعر الإجمالي: @{{total_price}} ريال</p>
            </div>

            <div style="margin-bottom: 20px;">
                <h2 style="color: #333; font-size: 20px; border-bottom: 2px solid #1db8f8; padding-bottom: 5px;">ملاحظات</h2>
                <p style="text-align: justify; color: #555;">[الملاحظات والشروط]</p>
            </div>
        </div>';
    }

    /**
     * قالب عرض سعر للتصميم
     */
    private function getDesignQuotationTemplate(): string
    {
        return '<div style="direction: rtl; text-align: right; font-family: Arial, sans-serif; padding: 40px; line-height: 1.8;">
            <div style="text-align: center; margin-bottom: 40px; border-bottom: 3px solid #1db8f8; padding-bottom: 20px;">
                <h1 style="color: #1db8f8; font-size: 32px; margin: 0 0 10px 0;">عرض سعر - خدمات التصميم</h1>
                <p style="color: #666; font-size: 16px; margin: 0;">@{{project_name}}</p>
            </div>

            <div style="margin-bottom: 30px;">
                <h2 style="color: #333; font-size: 24px; border-bottom: 2px solid #1db8f8; padding-bottom: 10px; margin-bottom: 20px;">معلومات المشروع</h2>
                <table style="width: 100%; border-collapse: collapse;">
                    <tr>
                        <td style="padding: 10px; background: #f5f5f5; font-weight: bold; width: 30%;">اسم العميل:</td>
                        <td style="padding: 10px;">@{{client_name}}</td>
                    </tr>
                    <tr>
                        <td style="padding: 10px; background: #f5f5f5; font-weight: bold;">اسم المشروع:</td>
                        <td style="padding: 10px;">@{{project_name}}</td>
                    </tr>
                    <tr>
                        <td style="padding: 10px; background: #f5f5f5; font-weight: bold;">نوع الخدمة:</td>
                        <td style="padding: 10px;">@{{service_name}}</td>
                    </tr>
                    <tr>
                        <td style="padding: 10px; background: #f5f5f5; font-weight: bold;">تاريخ العرض:</td>
                        <td style="padding: 10px;">@{{date}}</td>
                    </tr>
                </table>
            </div>

            <div style="margin-bottom: 30px;">
                <h2 style="color: #333; font-size: 24px; border-bottom: 2px solid #1db8f8; padding-bottom: 10px; margin-bottom: 20px;">خدمات التصميم المقدمة</h2>
                <ul style="color: #555; font-size: 16px; line-height: 1.8;">
                    <li>التصميم المعماري</li>
                    <li>التصميم الإنشائي</li>
                    <li>التصميم الكهربائي</li>
                    <li>التصميم الميكانيكي</li>
                    <li>رسومات التنفيذ</li>
                </ul>
            </div>

            <div style="margin-bottom: 30px; text-align: left;">
                <table style="width: 50%; margin-left: auto; border-collapse: collapse;">
                    <tr>
                        <td style="padding: 12px; background: #f5f5f5; font-weight: bold; font-size: 18px; border: 1px solid #ddd;">السعر الإجمالي:</td>
                        <td style="padding: 12px; font-size: 18px; font-weight: bold; color: #1db8f8; border: 1px solid #ddd;">@{{total_price}} ريال</td>
                    </tr>
                </table>
            </div>

            <div style="margin-bottom: 30px;">
                <h2 style="color: #333; font-size: 24px; border-bottom: 2px solid #1db8f8; padding-bottom: 10px; margin-bottom: 20px;">شروط وأحكام</h2>
                <ul style="color: #555; font-size: 16px; line-height: 1.8;">
                    <li>السعر يشمل جميع مراحل التصميم</li>
                    <li>يتم تسليم الرسومات النهائية بعد الموافقة</li>
                    <li>التعديلات البسيطة مجانية</li>
                    <li>هذا العرض ساري لمدة 30 يوم من تاريخه</li>
                </ul>
            </div>
        </div>';
    }

    /**
     * قالب عرض سعر للإشراف
     */
    private function getSupervisionQuotationTemplate(): string
    {
        return '<div style="direction: rtl; text-align: right; font-family: Arial, sans-serif; padding: 40px; line-height: 1.8;">
            <div style="text-align: center; margin-bottom: 40px; border-bottom: 3px solid #1db8f8; padding-bottom: 20px;">
                <h1 style="color: #1db8f8; font-size: 32px; margin: 0 0 10px 0;">عرض سعر - خدمات الإشراف</h1>
                <p style="color: #666; font-size: 16px; margin: 0;">@{{project_name}}</p>
            </div>

            <div style="margin-bottom: 30px;">
                <h2 style="color: #333; font-size: 24px; border-bottom: 2px solid #1db8f8; padding-bottom: 10px; margin-bottom: 20px;">معلومات المشروع</h2>
                <table style="width: 100%; border-collapse: collapse;">
                    <tr>
                        <td style="padding: 10px; background: #f5f5f5; font-weight: bold; width: 30%;">اسم العميل:</td>
                        <td style="padding: 10px;">@{{client_name}}</td>
                    </tr>
                    <tr>
                        <td style="padding: 10px; background: #f5f5f5; font-weight: bold;">اسم المشروع:</td>
                        <td style="padding: 10px;">@{{project_name}}</td>
                    </tr>
                    <tr>
                        <td style="padding: 10px; background: #f5f5f5; font-weight: bold;">نوع الخدمة:</td>
                        <td style="padding: 10px;">@{{service_name}}</td>
                    </tr>
                    <tr>
                        <td style="padding: 10px; background: #f5f5f5; font-weight: bold;">تاريخ العرض:</td>
                        <td style="padding: 10px;">@{{date}}</td>
                    </tr>
                </table>
            </div>

            <div style="margin-bottom: 30px;">
                <h2 style="color: #333; font-size: 24px; border-bottom: 2px solid #1db8f8; padding-bottom: 10px; margin-bottom: 20px;">خدمات الإشراف المقدمة</h2>
                <ul style="color: #555; font-size: 16px; line-height: 1.8;">
                    <li>الإشراف على التنفيذ</li>
                    <li>مراقبة جودة الأعمال</li>
                    <li>التنسيق مع المقاولين</li>
                    <li>إعداد تقارير المتابعة</li>
                    <li>الزيارات الميدانية</li>
                </ul>
            </div>

            <div style="margin-bottom: 30px; text-align: left;">
                <table style="width: 50%; margin-left: auto; border-collapse: collapse;">
                    <tr>
                        <td style="padding: 12px; background: #f5f5f5; font-weight: bold; font-size: 18px; border: 1px solid #ddd;">السعر الإجمالي:</td>
                        <td style="padding: 12px; font-size: 18px; font-weight: bold; color: #1db8f8; border: 1px solid #ddd;">@{{total_price}} ريال</td>
                    </tr>
                </table>
            </div>

            <div style="margin-bottom: 30px;">
                <h2 style="color: #333; font-size: 24px; border-bottom: 2px solid #1db8f8; padding-bottom: 10px; margin-bottom: 20px;">شروط وأحكام</h2>
                <ul style="color: #555; font-size: 16px; line-height: 1.8;">
                    <li>السعر يشمل جميع خدمات الإشراف المذكورة</li>
                    <li>مدة الإشراف حسب مدة المشروع</li>
                    <li>يتم الدفع على دفعات حسب الاتفاق</li>
                    <li>هذا العرض ساري لمدة 30 يوم من تاريخه</li>
                </ul>
            </div>
        </div>';
    }
}
