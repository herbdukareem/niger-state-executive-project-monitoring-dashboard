export interface Project {
  id: number;
  name: string;
  id_code: string;
  status: 'not_started' | 'in_progress' | 'on_hold' | 'completed' | 'cancelled';
  progress_percentage: number;
  total_budget: number;
  cumulative_expenditure: number;
  start_date: string;
  end_date: string;
  latitude?: number;
  longitude?: number;
  lga_id?: string;
  lga_name?: string;
  ward_id?: string;
  ward_name?: string;
  address?: string;
  location_description?: string;
  project_manager?: {
    id: number;
    name: string;
  };
  sector?: string;
  implementing_organization?: string;
}

export const sampleProjects: Project[] = [
  {
    id: 1,
    name: 'Water Infrastructure Development - Minna',
    id_code: 'WID-2024-001',
    status: 'in_progress',
    progress_percentage: 75,
    total_budget: 50000000,
    cumulative_expenditure: 37500000,
    start_date: '2024-01-15',
    end_date: '2024-12-31',
    latitude: 9.6167,
    longitude: 6.5500,
    lga_id: 'minna',
    lga_name: 'Minna',
    ward_id: 'minna_central',
    ward_name: 'Minna Central',
    address: 'Central Minna, Niger State',
    location_description: 'Main water treatment facility and distribution network',
    project_manager: { id: 1, name: 'John Doe' },
    sector: 'Water & Sanitation',
    implementing_organization: 'Niger State Water Board'
  },
  {
    id: 2,
    name: 'Rural Healthcare Center - Bida',
    id_code: 'RHC-2024-002',
    status: 'in_progress',
    progress_percentage: 45,
    total_budget: 25000000,
    cumulative_expenditure: 11250000,
    start_date: '2024-03-01',
    end_date: '2025-02-28',
    latitude: 9.0833,
    longitude: 6.0167,
    lga_id: 'bida',
    lga_name: 'Bida',
    ward_id: 'bida_central',
    ward_name: 'Bida Central',
    address: 'Bida Township, Niger State',
    location_description: 'Primary healthcare center with modern equipment',
    project_manager: { id: 2, name: 'Dr. Sarah Ahmed' },
    sector: 'Health',
    implementing_organization: 'Niger State Ministry of Health'
  },
  {
    id: 3,
    name: 'Agricultural Processing Center - Kontagora',
    id_code: 'APC-2024-003',
    status: 'completed',
    progress_percentage: 100,
    total_budget: 35000000,
    cumulative_expenditure: 34500000,
    start_date: '2023-06-01',
    end_date: '2024-05-31',
    latitude: 10.4000,
    longitude: 5.4667,
    lga_id: 'kontagora',
    lga_name: 'Kontagora',
    ward_id: 'kontagora_central',
    ward_name: 'Kontagora Central',
    address: 'Industrial Area, Kontagora',
    location_description: 'Modern rice and grain processing facility',
    project_manager: { id: 3, name: 'Engr. Mohammed Bello' },
    sector: 'Agriculture',
    implementing_organization: 'Niger State Agricultural Development Program'
  },
  {
    id: 4,
    name: 'Solar Power Grid Extension - Suleja',
    id_code: 'SPG-2024-004',
    status: 'in_progress',
    progress_percentage: 60,
    total_budget: 80000000,
    cumulative_expenditure: 48000000,
    start_date: '2024-02-01',
    end_date: '2024-11-30',
    latitude: 9.1833,
    longitude: 7.1833,
    lga_id: 'suleja',
    lga_name: 'Suleja',
    ward_id: 'suleja_a',
    ward_name: 'Suleja A',
    address: 'Suleja Industrial Layout',
    location_description: 'Solar power generation and distribution infrastructure',
    project_manager: { id: 4, name: 'Engr. Fatima Usman' },
    sector: 'Energy',
    implementing_organization: 'Niger State Power Company'
  },
  {
    id: 5,
    name: 'Road Construction - Agaie to Lapai',
    id_code: 'RCN-2024-005',
    status: 'in_progress',
    progress_percentage: 30,
    total_budget: 120000000,
    cumulative_expenditure: 36000000,
    start_date: '2024-04-01',
    end_date: '2025-03-31',
    latitude: 9.0167,
    longitude: 6.3333,
    lga_id: 'agaie',
    lga_name: 'Agaie',
    ward_id: 'agaie_central',
    ward_name: 'Agaie Central',
    address: 'Agaie-Lapai Highway',
    location_description: '45km dual carriageway with modern drainage systems',
    project_manager: { id: 5, name: 'Engr. Ibrahim Kolo' },
    sector: 'Infrastructure',
    implementing_organization: 'Niger State Ministry of Works'
  },
  {
    id: 6,
    name: 'Educational Technology Center - Bosso',
    id_code: 'ETC-2024-006',
    status: 'not_started',
    progress_percentage: 0,
    total_budget: 15000000,
    cumulative_expenditure: 0,
    start_date: '2024-10-01',
    end_date: '2025-09-30',
    latitude: 9.6000,
    longitude: 6.5167,
    lga_id: 'bosso',
    lga_name: 'Bosso',
    ward_id: 'bosso_central',
    ward_name: 'Bosso Central',
    address: 'Federal University of Technology Minna Campus',
    location_description: 'Modern ICT training center for students and professionals',
    project_manager: { id: 6, name: 'Dr. Aisha Garba' },
    sector: 'Education',
    implementing_organization: 'Niger State Ministry of Education'
  },
  {
    id: 7,
    name: 'Fish Farming Development - Shiroro',
    id_code: 'FFD-2024-007',
    status: 'in_progress',
    progress_percentage: 85,
    total_budget: 20000000,
    cumulative_expenditure: 17000000,
    start_date: '2024-01-01',
    end_date: '2024-12-31',
    latitude: 9.9500,
    longitude: 6.8333,
    lga_id: 'shiroro',
    lga_name: 'Shiroro',
    ward_id: 'shiroro_central',
    ward_name: 'Shiroro Central',
    address: 'Shiroro Lake Area',
    location_description: 'Commercial fish farming ponds and processing facility',
    project_manager: { id: 7, name: 'Dr. Yusuf Ndako' },
    sector: 'Agriculture',
    implementing_organization: 'Niger State Fisheries Development'
  },
  {
    id: 8,
    name: 'Market Infrastructure - Gbako',
    id_code: 'MKT-2024-008',
    status: 'on_hold',
    progress_percentage: 20,
    total_budget: 30000000,
    cumulative_expenditure: 6000000,
    start_date: '2024-05-01',
    end_date: '2025-04-30',
    latitude: 9.0500,
    longitude: 6.4167,
    lga_id: 'gbako',
    lga_name: 'Gbako',
    ward_id: 'gbako_central',
    ward_name: 'Gbako Central',
    address: 'Bida-Gbako Road Junction',
    location_description: 'Modern market complex with cold storage facilities',
    project_manager: { id: 8, name: 'Mallam Umar Sani' },
    sector: 'Commerce',
    implementing_organization: 'Niger State Commerce and Industry'
  },
  {
    id: 9,
    name: 'Youth Skills Center - Chanchaga',
    id_code: 'YSC-2024-009',
    status: 'in_progress',
    progress_percentage: 55,
    total_budget: 18000000,
    cumulative_expenditure: 9900000,
    start_date: '2024-03-15',
    end_date: '2025-02-14',
    latitude: 9.6333,
    longitude: 6.5833,
    lga_id: 'chanchaga',
    lga_name: 'Chanchaga',
    ward_id: 'chanchaga_central',
    ward_name: 'Chanchaga Central',
    address: 'Chanchaga Industrial Area',
    location_description: 'Vocational training center for youth empowerment',
    project_manager: { id: 9, name: 'Mrs. Hauwa Aliyu' },
    sector: 'Youth Development',
    implementing_organization: 'Niger State Youth Development Agency'
  },
  {
    id: 10,
    name: 'Waste Management System - Tafa',
    id_code: 'WMS-2024-010',
    status: 'completed',
    progress_percentage: 100,
    total_budget: 12000000,
    cumulative_expenditure: 11800000,
    start_date: '2023-09-01',
    end_date: '2024-08-31',
    latitude: 9.3167,
    longitude: 7.3667,
    lga_id: 'tafa',
    lga_name: 'Tafa',
    ward_id: 'tafa_central',
    ward_name: 'Tafa Central',
    address: 'Tafa Township',
    location_description: 'Integrated waste collection and recycling facility',
    project_manager: { id: 10, name: 'Engr. Musa Abdullahi' },
    sector: 'Environment',
    implementing_organization: 'Niger State Environmental Protection Agency'
  }
];

export default sampleProjects;
