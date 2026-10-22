<?php

namespace Database\Seeders;

use App\Models\LawReference;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class LawReferenceSeeder extends Seeder
{
    public function run(): void
    {
        $laws = [
            // ─── LEYES PARA EMPRESAS ───
            [
                'title' => 'Ley 1581 de 2012 - Protección de Datos Personales',
                'description' => 'Regula el tratamiento de datos personales en Colombia. Las empresas deben obtener consentimiento previo para recopilar datos de estudiantes y garantizar su protección.',
                'applicable_to' => ['company', 'admin'],
                'category' => 'Datos personales',
                'law_number' => 'Ley 1581 de 2012',
                'publication_date' => '2012-10-17',
                'relevant_articles' => [
                    'Art. 5' => 'Principios de recolección y tratamiento de datos',
                    'Art. 6' => 'Consentimiento informado previo',
                    'Art. 8' => 'Información a suministrar al usuario',
                ],
                'implementation_notes' => 'La empresa debe informar a los estudiantes sobre la recopilación y uso de sus datos antes de procesarlos.',
            ],
            [
                'title' => 'Código Sustantivo del Trabajo - Obligaciones del empleador',
                'description' => 'Define las obligaciones laborales de los empleadores con sus trabajadores, incluyendo afiliación a seguridad social, contratación, salario mínimo y condiciones de trabajo.',
                'applicable_to' => ['company', 'student', 'admin'],
                'category' => 'Laboral',
                'law_number' => 'Código Sustantivo del Trabajo',
                'publication_date' => '2024-01-01',
                'relevant_articles' => [
                    'Art. 54' => 'Definición del contrato de trabajo',
                    'Art. 102' => 'Salario mínimo legal mensual',
                    'Art. 202' => 'Afiliación a seguridad social integral',
                ],
                'implementation_notes' => 'Toda relación laboral debe cumplir con afiliación a seguridad social y garantizar condiciones seguras de trabajo.',
            ],
            [
                'title' => 'Resolución 2400 de 1979 - Estatuto de Seguridad Industrial',
                'description' => 'Establece las normas de higiene y seguridad en el trabajo. Las empresas deben garantizar ambientes seguros y saludables.',
                'applicable_to' => ['company', 'admin'],
                'category' => 'Seguridad laboral',
                'law_number' => 'Resolución 2400 de 1979',
                'publication_date' => '1979-05-22',
                'relevant_articles' => [
                    'Art. 2' => 'Ámbito de aplicación',
                    'Art. 7' => 'Obligaciones del empleador',
                    'Art. 9' => 'Responsabilidades del empleado',
                ],
                'implementation_notes' => 'Garantizar condiciones de seguridad, higiene y salud ocupacional en lugares de trabajo.',
            ],
            [
                'title' => 'Ley 1090 de 2006 - Ley de Psicología',
                'description' => 'Regula la profesión de psicología en Colombia. Aplica cuando se realicen evaluaciones psicológicas o de selección de personal.',
                'applicable_to' => ['company', 'admin'],
                'category' => 'Profesional',
                'law_number' => 'Ley 1090 de 2006',
                'publication_date' => '2006-09-06',
                'relevant_articles' => [
                    'Art. 2' => 'Definición y objeto de la profesión',
                    'Art. 31' => 'Confidencialidad y privacidad',
                ],
                'implementation_notes' => 'Si se utilizan tests psicológicos en selección, deben ser aplicados por profesionales autorizados.',
            ],
            [
                'title' => 'Ley 1780 de 2016 - Acceso al Empleo para Personas con Discapacidad',
                'description' => 'Promueve la inclusión laboral de personas con discapacidad. Las empresas deben garantizar no discriminación en procesos de selección.',
                'applicable_to' => ['company', 'admin'],
                'category' => 'Inclusión laboral',
                'law_number' => 'Ley 1780 de 2016',
                'publication_date' => '2016-02-19',
                'relevant_articles' => [
                    'Art. 2' => 'Acceso al empleo sin discriminación',
                    'Art. 3' => 'Obligaciones de inclusión',
                ],
                'implementation_notes' => 'No discriminar a candidatos por motivo de discapacidad y promover accesibilidad.',
            ],

            // ─── LEYES PARA ESTUDIANTES ───
            [
                'title' => 'Ley 30 de 1992 - Educación Superior en Colombia',
                'description' => 'Marco legal de la educación superior. Define derechos y deberes de estudiantes de universidades colombianas.',
                'applicable_to' => ['student', 'admin'],
                'category' => 'Educación',
                'law_number' => 'Ley 30 de 1992',
                'publication_date' => '1992-12-28',
                'relevant_articles' => [
                    'Art. 1' => 'Definición de educación superior',
                    'Art. 31' => 'Derechos de los estudiantes',
                    'Art. 41' => 'Deberes de los estudiantes',
                ],
                'implementation_notes' => 'Los estudiantes tienen derechos a una educación de calidad y la responsabilidad de cumplir normas institucionales.',
            ],
            [
                'title' => 'Decreto 1295 de 1994 - Sistema General de Riesgos Profesionales',
                'description' => 'Protege a trabajadores ante riesgos profesionales. Aplica a practicantes y estudiantes que realicen actividades laborales.',
                'applicable_to' => ['student', 'company', 'admin'],
                'category' => 'Seguridad laboral',
                'law_number' => 'Decreto 1295 de 1994',
                'publication_date' => '1994-06-22',
                'relevant_articles' => [
                    'Art. 3' => 'Definición de accidente de trabajo',
                    'Art. 9' => 'Cobertura del sistema',
                ],
                'implementation_notes' => 'Practicantes y estudiantes en vacantes deben estar cubiertos por seguro de riesgos profesionales.',
            ],
            [
                'title' => 'Ley 1581 de 2012 - Tus Derechos sobre tus Datos',
                'description' => 'Como estudiante, tienes derechos sobre tus datos personales. Las empresas deben respetar tu privacidad y darte acceso a tu información.',
                'applicable_to' => ['student', 'admin'],
                'category' => 'Datos personales',
                'law_number' => 'Ley 1581 de 2012',
                'publication_date' => '2012-10-17',
                'relevant_articles' => [
                    'Art. 8' => 'Derechos del titular de datos',
                    'Art. 8.2.1' => 'Conocer qué información se recopila',
                    'Art. 8.2.7' => 'Solicitar rectificación de datos inexactos',
                ],
                'implementation_notes' => 'Tienes derecho a conocer, actualizar y rectificar tus datos personales en cualquier momento.',
            ],
            [
                'title' => 'Código de Comercio - Derechos del Consumidor',
                'description' => 'Como usuario de servicios, tienes derechos de consumidor. Las plataformas de empleo deben ser transparentes en sus términos y condiciones.',
                'applicable_to' => ['student', 'admin'],
                'category' => 'Derechos del consumidor',
                'law_number' => 'Código de Comercio',
                'publication_date' => '2024-01-01',
                'relevant_articles' => [
                    'Art. 1' => 'Definición de consumidor',
                    'Art. 2' => 'Derechos del consumidor',
                ],
                'implementation_notes' => 'Tienes derecho a información clara, buena fe y protección ante prácticas engañosas.',
            ],

            // ─── LEYES PARA ADMINISTRADORES ───
            [
                'title' => 'Ley 1379 de 2010 - Generación de Empleo',
                'description' => 'Promueve la creación de empleo y fomenta iniciativas de emprendimiento. Los administrativos deben conocer estas políticas.',
                'applicable_to' => ['admin', 'company'],
                'category' => 'Empleo',
                'law_number' => 'Ley 1379 de 2010',
                'publication_date' => '2010-11-10',
                'relevant_articles' => [
                    'Art. 1' => 'Objeto de la ley',
                    'Art. 2' => 'Beneficios tributarios',
                ],
                'implementation_notes' => 'Conocer incentivos para empresas que generan empleo formal.',
            ],
            [
                'title' => 'Ley 1857 de 2017 - Seguridad de la Información',
                'description' => 'Protege datos en plataformas digitales. Los administradores deben garantizar seguridad de bases de datos de usuarios y empresas.',
                'applicable_to' => ['admin'],
                'category' => 'Ciberseguridad',
                'law_number' => 'Ley 1857 de 2017',
                'publication_date' => '2017-07-18',
                'relevant_articles' => [
                    'Art. 1' => 'Seguridad digital',
                    'Art. 2' => 'Protección de sistemas',
                ],
                'implementation_notes' => 'Implementar medidas técnicas para proteger información de estudiantes y empresas.',
            ],
            [
                'title' => 'Decreto 1377 de 2013 - Habeas Data',
                'description' => 'Reglamenta el acceso a datos personales y derecho a saber qué información se tiene. Los administradores deben garantizar esto.',
                'applicable_to' => ['admin'],
                'category' => 'Datos personales',
                'law_number' => 'Decreto 1377 de 2013',
                'publication_date' => '2013-09-27',
                'relevant_articles' => [
                    'Art. 2' => 'Procedimiento de solicitud de acceso',
                    'Art. 4' => 'Derechos de petición',
                ],
                'implementation_notes' => 'Crear procesos para que usuarios puedan solicitar sus datos y rectificaciones.',
            ],
        ];

        foreach ($laws as $lawData) {
            LawReference::create($lawData);
        }

        $this->command->info('✅ Leyes colombianas añadidas correctamente.');
    }
}
