export interface Ward {
  id: string;
  name: string;
  code: string;
}

export interface LocalGovernmentArea {
  id: string;
  name: string;
  code: string;
  headquarters: string;
  area_km2: number;
  population_estimate: number;
  wards: Ward[];
  coordinates: {
    latitude: number;
    longitude: number;
  };
}

import { additionalNigerStateLGAs } from './nigerStateExtended';

const baseLGAs: LocalGovernmentArea[] = [
  {
    id: "agaie",
    name: "Agaie",
    code: "AGA",
    headquarters: "Agaie",
    area_km2: 1903,
    population_estimate: 132312,
    coordinates: { latitude: 9.0167, longitude: 6.1833 },
    wards: [
      { id: "agaie_01", name: "Agaie", code: "AGA01" },
      { id: "agaie_02", name: "Baro", code: "AGA02" },
      { id: "agaie_03", name: "Ekossa", code: "AGA03" },
      { id: "agaie_04", name: "Etsugaie", code: "AGA04" },
      { id: "agaie_05", name: "Katcha", code: "AGA05" },
      { id: "agaie_06", name: "Lapai", code: "AGA06" },
      { id: "agaie_07", name: "Magaji", code: "AGA07" },
      { id: "agaie_08", name: "Tagagi", code: "AGA08" },
      { id: "agaie_09", name: "Tswata", code: "AGA09" },
      { id: "agaie_10", name: "Yuna", code: "AGA10" }
    ]
  },
  {
    id: "agwara",
    name: "Agwara",
    code: "AGW",
    headquarters: "Agwara",
    area_km2: 2492,
    population_estimate: 58579,
    coordinates: { latitude: 11.0167, longitude: 4.3833 },
    wards: [
      { id: "agwara_01", name: "Agwara", code: "AGW01" },
      { id: "agwara_02", name: "Gallah", code: "AGW02" },
      { id: "agwara_03", name: "Kokani", code: "AGW03" },
      { id: "agwara_04", name: "Mago", code: "AGW04" },
      { id: "agwara_05", name: "Papiri", code: "AGW05" },
      { id: "agwara_06", name: "Rofia", code: "AGW06" },
      { id: "agwara_07", name: "Suteku", code: "AGW07" },
      { id: "agwara_08", name: "Swashi", code: "AGW08" },
      { id: "agwara_09", name: "Takuma", code: "AGW09" },
      { id: "agwara_10", name: "Yanigi", code: "AGW10" }
    ]
  },
  {
    id: "bida",
    name: "Bida",
    code: "BID",
    headquarters: "Bida",
    area_km2: 1180,
    population_estimate: 188181,
    coordinates: { latitude: 9.0833, longitude: 6.0167 },
    wards: [
      { id: "bida_01", name: "Bariki", code: "BID01" },
      { id: "bida_02", name: "Bida I", code: "BID02" },
      { id: "bida_03", name: "Bida II", code: "BID03" },
      { id: "bida_04", name: "Bida III", code: "BID04" },
      { id: "bida_05", name: "Dokodza", code: "BID05" },
      { id: "bida_06", name: "Kyari", code: "BID06" },
      { id: "bida_07", name: "Landzun", code: "BID07" },
      { id: "bida_08", name: "Masaba I", code: "BID08" },
      { id: "bida_09", name: "Masaba II", code: "BID09" },
      { id: "bida_10", name: "Mayaki Ndajiya", code: "BID10" },
      { id: "bida_11", name: "Nassarafu", code: "BID11" }
    ]
  },
  {
    id: "borgu",
    name: "Borgu",
    code: "BOR",
    headquarters: "New Bussa",
    area_km2: 17321,
    population_estimate: 131555,
    coordinates: { latitude: 10.2000, longitude: 4.5000 },
    wards: [
      { id: "borgu_01", name: "Babanna", code: "BOR01" },
      { id: "borgu_02", name: "Dugga", code: "BOR02" },
      { id: "borgu_03", name: "Kaoje", code: "BOR03" },
      { id: "borgu_04", name: "Konkoso", code: "BOR04" },
      { id: "borgu_05", name: "Malale", code: "BOR05" },
      { id: "borgu_06", name: "New Bussa", code: "BOR06" },
      { id: "borgu_07", name: "Shagunu", code: "BOR07" },
      { id: "borgu_08", name: "Wawa", code: "BOR08" },
      { id: "borgu_09", name: "Yelwa", code: "BOR09" },
      { id: "borgu_10", name: "Zugurma", code: "BOR10" }
    ]
  },
  {
    id: "bosso",
    name: "Bosso",
    code: "BOS",
    headquarters: "Maikunkele",
    area_km2: 1592,
    population_estimate: 147359,
    coordinates: { latitude: 9.6167, longitude: 6.5500 },
    wards: [
      { id: "bosso_01", name: "Beji", code: "BOS01" },
      { id: "bosso_02", name: "Bosso", code: "BOS02" },
      { id: "bosso_03", name: "Central", code: "BOS03" },
      { id: "bosso_04", name: "Garatu", code: "BOS04" },
      { id: "bosso_05", name: "Kodo", code: "BOS05" },
      { id: "bosso_06", name: "Maikunkele", code: "BOS06" },
      { id: "bosso_07", name: "Maitumbi", code: "BOS07" },
      { id: "bosso_08", name: "Shaba", code: "BOS08" },
      { id: "bosso_09", name: "Tudun Fulani", code: "BOS09" },
      { id: "bosso_10", name: "Yankee", code: "BOS10" }
    ]
  },
  {
    id: "chanchaga",
    name: "Chanchaga",
    code: "CHA",
    headquarters: "Minna",
    area_km2: 1669,
    population_estimate: 201429,
    coordinates: { latitude: 9.6167, longitude: 6.5500 },
    wards: [
      { id: "chanchaga_01", name: "Barkin Sale", code: "CHA01" },
      { id: "chanchaga_02", name: "Chanchaga", code: "CHA02" },
      { id: "chanchaga_03", name: "Dutsen Kura Gwari", code: "CHA03" },
      { id: "chanchaga_04", name: "Dutsen Kura Hausa", code: "CHA04" },
      { id: "chanchaga_05", name: "Gwari", code: "CHA05" },
      { id: "chanchaga_06", name: "Kpakungu", code: "CHA06" },
      { id: "chanchaga_07", name: "Limawa", code: "CHA07" },
      { id: "chanchaga_08", name: "Maitumbi", code: "CHA08" },
      { id: "chanchaga_09", name: "Minna Centre", code: "CHA09" },
      { id: "chanchaga_10", name: "Nasarawa", code: "CHA10" },
      { id: "chanchaga_11", name: "Sauka Kahuta", code: "CHA11" }
    ]
  },
  {
    id: "edati",
    name: "Edati",
    code: "EDA",
    headquarters: "Enagi",
    area_km2: 1188,
    population_estimate: 88867,
    coordinates: { latitude: 9.1500, longitude: 6.2000 },
    wards: [
      { id: "edati_01", name: "Batati", code: "EDA01" },
      { id: "edati_02", name: "Doko", code: "EDA02" },
      { id: "edati_03", name: "Enagi", code: "EDA03" },
      { id: "edati_04", name: "Etsugba", code: "EDA04" },
      { id: "edati_05", name: "Gadayi", code: "EDA05" },
      { id: "edati_06", name: "Lemu", code: "EDA06" },
      { id: "edati_07", name: "Sakaba", code: "EDA07" },
      { id: "edati_08", name: "Shabamaliki", code: "EDA08" },
      { id: "edati_09", name: "Tsohon Kabula", code: "EDA09" },
      { id: "edati_10", name: "Wamba", code: "EDA10" }
    ]
  },
  {
    id: "gbako",
    name: "Gbako",
    code: "GBA",
    headquarters: "Lemu",
    area_km2: 1499,
    population_estimate: 147011,
    coordinates: { latitude: 9.1000, longitude: 6.0500 },
    wards: [
      { id: "gbako_01", name: "Dendo", code: "GBA01" },
      { id: "gbako_02", name: "Gbako", code: "GBA02" },
      { id: "gbako_03", name: "Kataeregi", code: "GBA03" },
      { id: "gbako_04", name: "Kwagana", code: "GBA04" },
      { id: "gbako_05", name: "Lemu", code: "GBA05" },
      { id: "gbako_06", name: "Lesu", code: "GBA06" },
      { id: "gbako_07", name: "Muye", code: "GBA07" },
      { id: "gbako_08", name: "Paiko Central", code: "GBA08" },
      { id: "gbako_09", name: "Paiko East", code: "GBA09" },
      { id: "gbako_10", name: "Paiko West", code: "GBA10" }
    ]
  },
  {
    id: "gurara",
    name: "Gurara",
    code: "GUR",
    headquarters: "Gawu Babangida",
    area_km2: 1235,
    population_estimate: 85670,
    coordinates: { latitude: 9.3000, longitude: 6.8000 },
    wards: [
      { id: "gurara_01", name: "Alawa", code: "GUR01" },
      { id: "gurara_02", name: "Beji", code: "GUR02" },
      { id: "gurara_03", name: "Diko", code: "GUR03" },
      { id: "gurara_04", name: "Gawu Babangida", code: "GUR04" },
      { id: "gurara_05", name: "Guni", code: "GUR05" },
      { id: "gurara_06", name: "Izom", code: "GUR06" },
      { id: "gurara_07", name: "Kwaki", code: "GUR07" },
      { id: "gurara_08", name: "Lambata", code: "GUR08" },
      { id: "gurara_09", name: "Leaba", code: "GUR09" },
      { id: "gurara_10", name: "Tufa", code: "GUR10" }
    ]
  },
  {
    id: "katcha",
    name: "Katcha",
    code: "KAT",
    headquarters: "Katcha",
    area_km2: 2710,
    population_estimate: 135008,
    coordinates: { latitude: 8.8000, longitude: 6.2000 },
    wards: [
      { id: "katcha_01", name: "Badeggi", code: "KAT01" },
      { id: "katcha_02", name: "Baddna", code: "KAT02" },
      { id: "katcha_03", name: "Dzwafu", code: "KAT03" },
      { id: "katcha_04", name: "Emi", code: "KAT04" },
      { id: "katcha_05", name: "Jima", code: "KAT05" },
      { id: "katcha_06", name: "Katcha", code: "KAT06" },
      { id: "katcha_07", name: "Kpada", code: "KAT07" },
      { id: "katcha_08", name: "Sabon Gari", code: "KAT08" },
      { id: "katcha_09", name: "Takuti", code: "KAT09" },
      { id: "katcha_10", name: "Tswako", code: "KAT10" }
    ]
  },
  {
    id: "kontagora",
    name: "Kontagora",
    code: "KON",
    headquarters: "Kontagora",
    area_km2: 3718,
    population_estimate: 149603,
    coordinates: { latitude: 10.4000, longitude: 5.4667 },
    wards: [
      { id: "kontagora_01", name: "Auna", code: "KON01" },
      { id: "kontagora_02", name: "Fadama", code: "KON02" },
      { id: "kontagora_03", name: "Gulbin Boka", code: "KON03" },
      { id: "kontagora_04", name: "Jikantoro", code: "KON04" },
      { id: "kontagora_05", name: "Kontagora I", code: "KON05" },
      { id: "kontagora_06", name: "Kontagora II", code: "KON06" },
      { id: "kontagora_07", name: "Mayanka", code: "KON07" },
      { id: "kontagora_08", name: "Rafi", code: "KON08" },
      { id: "kontagora_09", name: "Tudun Wada", code: "KON09" },
      { id: "kontagora_10", name: "Wushishi", code: "KON10" }
    ]
  }
];

// Combine all LGAs
export const nigerStateLGAs: LocalGovernmentArea[] = [...baseLGAs, ...additionalNigerStateLGAs];

// Helper functions
export const getLGAByCode = (code: string): LocalGovernmentArea | undefined => {
  return nigerStateLGAs.find(lga => lga.code === code);
};

export const getLGAById = (id: string): LocalGovernmentArea | undefined => {
  return nigerStateLGAs.find(lga => lga.id === id);
};

export const getWardsByLGA = (lgaId: string): Ward[] => {
  const lga = getLGAById(lgaId);
  return lga ? lga.wards : [];
};

export const getAllWards = (): Ward[] => {
  return nigerStateLGAs.flatMap(lga => lga.wards);
};

export const searchLGAs = (query: string): LocalGovernmentArea[] => {
  const searchTerm = query.toLowerCase();
  return nigerStateLGAs.filter(lga => 
    lga.name.toLowerCase().includes(searchTerm) ||
    lga.headquarters.toLowerCase().includes(searchTerm) ||
    lga.code.toLowerCase().includes(searchTerm)
  );
};

export const searchWards = (query: string, lgaId?: string): Ward[] => {
  const searchTerm = query.toLowerCase();
  const wards = lgaId ? getWardsByLGA(lgaId) : getAllWards();
  return wards.filter(ward => 
    ward.name.toLowerCase().includes(searchTerm) ||
    ward.code.toLowerCase().includes(searchTerm)
  );
};
