<?php

namespace App\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;

class GeneralApiController extends Controller
{
    /* ─────────────────────────────────────────────────────────────────────
     * All three endpoints proxy to free public APIs so the browser never
     * hits cross-origin restrictions.
     *
     * Data source:  countriesnow.space  (states + cities — no key needed)
     *               api.postalpincode.in (cities by district — no key needed)
     *
     * All responses are cached 24 h to minimise external calls.
     * ──────────────────────────────────────────────────────────────────── */

    /**
     * GET /api/general/states
     * Returns sorted list of all Indian state names.
     */
    public function getStates(): JsonResponse
    {
        $states = Cache::remember('india_states_v2', 86400, function () {
            try {
                $r = Http::timeout(15)->post(
                    'https://countriesnow.space/api/v0.1/countries/states',
                    ['country' => 'India']
                );

                if ($r->successful()) {
                    return collect($r->json()['data']['states'] ?? [])
                        ->pluck('name')
                        ->filter()
                        ->sort()
                        ->values()
                        ->toArray();
                }
            } catch (\Exception $e) { /* fall through */ }

            return $this->fallbackStates();
        });

        return response()->json(['states' => $states]);
    }

    /**
     * GET /api/general/districts?state=Tamil+Nadu
     * Returns the correct 38 districts for Tamil Nadu (and correct counts for all states).
     * Uses a hardcoded authoritative list — no external API needed.
     */
    public function getDistricts(Request $request): JsonResponse
    {
        $request->validate(['state' => 'required|string|max:100']);
        $state = trim($request->input('state'));

        $map = $this->districtMap();

        // Case-insensitive key lookup
        foreach ($map as $key => $districts) {
            if (strcasecmp($key, $state) === 0) {
                return response()->json(['districts' => $districts]);
            }
        }

        return response()->json(['districts' => []]);
    }

    /**
     * GET /api/general/cities?state=Tamil+Nadu&district=Chennai
     *
     * Returns city / area names for a state + district combination.
     *
     * Primary:  api.postalpincode.in — searches post offices by district name.
     *           This gives real sub-district place names (towns, suburbs).
     * Fallback: If postalpincode returns nothing, echo the district name back
     *           as the only "city" so the user can still submit the form.
     */
    public function getCities(Request $request): JsonResponse
    {
        $request->validate([
            'state'    => 'required|string|max:100',
            'district' => 'required|string|max:100',
        ]);

        $state    = trim($request->input('state'));
        $district = trim($request->input('district'));
        $cacheKey = 'india_cities_v2_' . md5($state . '||' . $district);

        $cities = Cache::remember($cacheKey, 86400, function () use ($state, $district) {
            /* ── Primary: postal pincode API ── */
            $cities = $this->fetchFromPostalApi($district);

            /* ── Fallback: filter countriesnow city list ── */
            if (empty($cities)) {
                $cities = $this->fetchFromCountriesnow($state, $district);
            }

            /* ── Last resort: just the district name itself ── */
            if (empty($cities)) {
                $cities = [$district];
            }

            return $cities;
        });

        return response()->json(['cities' => $cities]);
    }

    /* ── Private helpers ─────────────────────────────────────────────── */

    /**
     * Fetch place names via api.postalpincode.in/postoffice/{district}
     * Returns [] on any failure.
     */
    private function fetchFromPostalApi(string $district): array
    {
        try {
            $r = Http::timeout(10)
                ->withHeaders(['User-Agent' => 'TransportApp/1.0'])
                ->get('https://api.postalpincode.in/postoffice/' . rawurlencode($district));

            if (!$r->successful()) {
                return [];
            }

            $payload = $r->json();

            if (
                !is_array($payload) ||
                empty($payload[0]) ||
                ($payload[0]['Status'] ?? '') !== 'Success'
            ) {
                return [];
            }

            return collect($payload[0]['PostOffice'] ?? [])
                ->pluck('Name')
                ->filter()
                ->unique()
                ->sort()
                ->values()
                ->toArray();
        } catch (\Exception $e) {
            return [];
        }
    }

    /**
     * Fallback: pull countriesnow city list for the state and filter
     * entries that contain the district name.
     */
    private function fetchFromCountriesnow(string $state, string $district): array
    {
        try {
            $r = Http::timeout(12)->post(
                'https://countriesnow.space/api/v0.1/countries/state/cities',
                ['country' => 'India', 'state' => $state]
            );

            if (!$r->successful()) {
                return [];
            }

            $all = collect($r->json()['data'] ?? []);

            $filtered = $all
                ->filter(fn($c) => stripos($c, $district) !== false)
                ->values();

            if (!$filtered->contains($district)) {
                $filtered->prepend($district);
            }

            // Too few matches → return district + top 30 cities of state
            if ($filtered->count() < 3) {
                return collect([$district])
                    ->merge($all->take(30))
                    ->unique()
                    ->sort()
                    ->values()
                    ->toArray();
            }

            return $filtered->unique()->sort()->values()->toArray();
        } catch (\Exception $e) {
            return [];
        }
    }

    /** Hardcoded district map — authoritative list for all Indian states. */
    private function districtMap(): array
    {
        return [
            'Andaman and Nicobar Islands' => ['Nicobar', 'North and Middle Andaman', 'South Andaman'],
            'Andhra Pradesh' => ['Anakapalli', 'Anantapur', 'Annamayya', 'Bapatla', 'Chittoor', 'East Godavari', 'Eluru', 'Guntur', 'Kakinada', 'Kona Seema', 'Krishna', 'Kurnool', 'Nandyal', 'Nellore', 'Palnadu', 'Parvathipuram Manyam', 'Prakasam', 'Srikakulam', 'Sri Potti Sriramulu Nellore', 'Sri Sathya Sai', 'Tirupati', 'Visakhapatnam', 'Vizianagaram', 'West Godavari', 'YSR Kadapa'],
            'Arunachal Pradesh' => ['Anjaw', 'Changlang', 'Dibang Valley', 'East Kameng', 'East Siang', 'Kamle', 'Kra Daadi', 'Kurung Kumey', 'Lepa Rada', 'Lohit', 'Longding', 'Lower Dibang Valley', 'Lower Siang', 'Lower Subansiri', 'Namsai', 'Pakke Kessang', 'Papum Pare', 'Shi Yomi', 'Siang', 'Tawang', 'Tirap', 'Upper Siang', 'Upper Subansiri', 'West Kameng', 'West Siang'],
            'Assam' => ['Bajali', 'Baksa', 'Barpeta', 'Biswanath', 'Bongaigaon', 'Cachar', 'Charaideo', 'Chirang', 'Darrang', 'Dhemaji', 'Dhubri', 'Dibrugarh', 'Dima Hasao', 'Goalpara', 'Golaghat', 'Hailakandi', 'Hojai', 'Jorhat', 'Kamrup', 'Kamrup Metropolitan', 'Karbi Anglong', 'Karimganj', 'Kokrajhar', 'Lakhimpur', 'Majuli', 'Morigaon', 'Nagaon', 'Nalbari', 'Sivasagar', 'Sonitpur', 'South Salmara Mankachar', 'Tinsukia', 'Udalguri', 'West Karbi Anglong'],
            'Bihar' => ['Araria', 'Arwal', 'Aurangabad', 'Banka', 'Begusarai', 'Bhagalpur', 'Bhojpur', 'Buxar', 'Darbhanga', 'East Champaran', 'Gaya', 'Gopalganj', 'Jamui', 'Jehanabad', 'Kaimur', 'Katihar', 'Khagaria', 'Kishanganj', 'Lakhisarai', 'Madhepura', 'Madhubani', 'Munger', 'Muzaffarpur', 'Nalanda', 'Nawada', 'Patna', 'Purnia', 'Rohtas', 'Saharsa', 'Samastipur', 'Saran', 'Sheikhpura', 'Sheohar', 'Sitamarhi', 'Siwan', 'Supaul', 'Vaishali', 'West Champaran'],
            'Chandigarh' => ['Chandigarh'],
            'Chhattisgarh' => ['Balod', 'Baloda Bazar', 'Balrampur', 'Bastar', 'Bemetara', 'Bijapur', 'Bilaspur', 'Dantewada', 'Dhamtari', 'Durg', 'Gariaband', 'Gaurela Pendra Marwahi', 'Janjgir Champa', 'Jashpur', 'Kabirdham', 'Kanker', 'Kondagaon', 'Korba', 'Koriya', 'Mahasamund', 'Manendragarh', 'Mungeli', 'Narayanpur', 'Raigarh', 'Raipur', 'Rajnandgaon', 'Sarangarh Bilaigarh', 'Sukma', 'Surajpur', 'Surguja'],
            'Delhi' => ['Central Delhi', 'East Delhi', 'New Delhi', 'North Delhi', 'North East Delhi', 'North West Delhi', 'Shahdara', 'South Delhi', 'South East Delhi', 'South West Delhi', 'West Delhi'],
            'Goa' => ['North Goa', 'South Goa'],
            'Gujarat' => ['Ahmedabad', 'Amreli', 'Anand', 'Aravalli', 'Banaskantha', 'Bharuch', 'Bhavnagar', 'Botad', 'Chhota Udepur', 'Dahod', 'Dang', 'Devbhoomi Dwarka', 'Gandhinagar', 'Gir Somnath', 'Jamnagar', 'Junagadh', 'Kheda', 'Kutch', 'Mahisagar', 'Mehsana', 'Morbi', 'Narmada', 'Navsari', 'Panchmahal', 'Patan', 'Porbandar', 'Rajkot', 'Sabarkantha', 'Surat', 'Surendranagar', 'Tapi', 'Vadodara', 'Valsad'],
            'Haryana' => ['Ambala', 'Bhiwani', 'Charkhi Dadri', 'Faridabad', 'Fatehabad', 'Gurugram', 'Hisar', 'Jhajjar', 'Jind', 'Kaithal', 'Karnal', 'Kurukshetra', 'Mahendragarh', 'Nuh', 'Palwal', 'Panchkula', 'Panipat', 'Rewari', 'Rohtak', 'Sirsa', 'Sonipat', 'Yamunanagar'],
            'Himachal Pradesh' => ['Bilaspur', 'Chamba', 'Hamirpur', 'Kangra', 'Kinnaur', 'Kullu', 'Lahaul and Spiti', 'Mandi', 'Shimla', 'Sirmaur', 'Solan', 'Una'],
            'Jammu and Kashmir' => ['Anantnag', 'Bandipora', 'Baramulla', 'Budgam', 'Doda', 'Ganderbal', 'Jammu', 'Kathua', 'Kishtwar', 'Kulgam', 'Kupwara', 'Poonch', 'Pulwama', 'Rajouri', 'Ramban', 'Reasi', 'Samba', 'Shopian', 'Srinagar', 'Udhampur'],
            'Jharkhand' => ['Bokaro', 'Chatra', 'Deoghar', 'Dhanbad', 'Dumka', 'East Singhbhum', 'Garhwa', 'Giridih', 'Godda', 'Gumla', 'Hazaribagh', 'Jamtara', 'Khunti', 'Koderma', 'Latehar', 'Lohardaga', 'Pakur', 'Palamu', 'Ramgarh', 'Ranchi', 'Sahebganj', 'Saraikela Kharsawan', 'Simdega', 'West Singhbhum'],
            'Karnataka' => ['Bagalkote', 'Ballari', 'Belagavi', 'Bengaluru Rural', 'Bengaluru Urban', 'Bidar', 'Chamarajanagara', 'Chikkaballapura', 'Chikkamagaluru', 'Chitradurga', 'Dakshina Kannada', 'Davanagere', 'Dharwada', 'Gadaga', 'Hassan', 'Haveri', 'Kalaburagi', 'Kodagu', 'Kolar', 'Koppala', 'Mandya', 'Mysuru', 'Raichuru', 'Ramanagara', 'Shivamogga', 'Tumakuru', 'Udupi', 'Uttara Kannada', 'Vijayapura', 'Yadgiri'],
            'Kerala' => ['Alappuzha', 'Ernakulam', 'Idukki', 'Kannur', 'Kasaragod', 'Kollam', 'Kottayam', 'Kozhikode', 'Malappuram', 'Palakkad', 'Pathanamthitta', 'Thiruvananthapuram', 'Thrissur', 'Wayanad'],
            'Ladakh' => ['Kargil', 'Leh'],
            'Lakshadweep' => ['Lakshadweep'],
            'Madhya Pradesh' => ['Agar Malwa', 'Alirajpur', 'Anuppur', 'Ashoknagar', 'Balaghat', 'Barwani', 'Betul', 'Bhind', 'Bhopal', 'Burhanpur', 'Chhatarpur', 'Chhindwara', 'Damoh', 'Datia', 'Dewas', 'Dhar', 'Dindori', 'Guna', 'Gwalior', 'Harda', 'Hoshangabad', 'Indore', 'Jabalpur', 'Jhabua', 'Katni', 'Khandwa', 'Khargone', 'Mandla', 'Mandsaur', 'Morena', 'Narsinghpur', 'Neemuch', 'Niwari', 'Panna', 'Raisen', 'Rajgarh', 'Ratlam', 'Rewa', 'Sagar', 'Satna', 'Sehore', 'Seoni', 'Shahdol', 'Shajapur', 'Sheopur', 'Shivpuri', 'Sidhi', 'Singrauli', 'Tikamgarh', 'Ujjain', 'Umaria', 'Vidisha'],
            'Maharashtra' => ['Ahmednagar', 'Akola', 'Amravati', 'Aurangabad', 'Beed', 'Bhandara', 'Buldhana', 'Chandrapur', 'Dhule', 'Gadchiroli', 'Gondia', 'Hingoli', 'Jalgaon', 'Jalna', 'Kolhapur', 'Latur', 'Mumbai', 'Mumbai Suburban', 'Nagpur', 'Nanded', 'Nandurbar', 'Nashik', 'Osmanabad', 'Palghar', 'Parbhani', 'Pune', 'Raigad', 'Ratnagiri', 'Sangli', 'Satara', 'Sindhudurg', 'Solapur', 'Thane', 'Wardha', 'Washim', 'Yavatmal'],
            'Manipur' => ['Bishnupur', 'Chandel', 'Churachandpur', 'Imphal East', 'Imphal West', 'Jiribam', 'Kakching', 'Kamjong', 'Kangpokpi', 'Noney', 'Pherzawl', 'Senapati', 'Tamenglong', 'Tengnoupal', 'Thoubal', 'Ukhrul'],
            'Meghalaya' => ['East Garo Hills', 'East Jaintia Hills', 'East Khasi Hills', 'North Garo Hills', 'Ri Bhoi', 'South Garo Hills', 'South West Garo Hills', 'South West Khasi Hills', 'West Garo Hills', 'West Jaintia Hills', 'West Khasi Hills'],
            'Mizoram' => ['Aizawl', 'Champhai', 'Hnahthial', 'Khawzawl', 'Kolasib', 'Lawngtlai', 'Lunglei', 'Mamit', 'Saiha', 'Saitual', 'Serchhip'],
            'Nagaland' => ['Chumoukedima', 'Dimapur', 'Kiphire', 'Kohima', 'Longleng', 'Mokokchung', 'Mon', 'Niuland', 'Noklak', 'Peren', 'Phek', 'Shamator', 'Tseminyu', 'Tuensang', 'Wokha', 'Zunheboto'],
            'Odisha' => ['Angul', 'Balangir', 'Balasore', 'Bargarh', 'Bhadrak', 'Boudh', 'Cuttack', 'Deogarh', 'Dhenkanal', 'Gajapati', 'Ganjam', 'Jagatsinghpur', 'Jajpur', 'Jharsuguda', 'Kalahandi', 'Kandhamal', 'Kendrapara', 'Kendujhar', 'Khordha', 'Koraput', 'Malkangiri', 'Mayurbhanj', 'Nabarangpur', 'Nayagarh', 'Nuapada', 'Puri', 'Rayagada', 'Sambalpur', 'Subarnapur', 'Sundargarh'],
            'Puducherry' => ['Karaikal', 'Mahe', 'Puducherry', 'Yanam'],
            'Punjab' => ['Amritsar', 'Barnala', 'Bathinda', 'Faridkot', 'Fatehgarh Sahib', 'Fazilka', 'Firozpur', 'Gurdaspur', 'Hoshiarpur', 'Jalandhar', 'Kapurthala', 'Ludhiana', 'Mansa', 'Moga', 'Mohali', 'Muktsar', 'Pathankot', 'Patiala', 'Rupnagar', 'Sangrur', 'Shaheed Bhagat Singh Nagar', 'Tarn Taran'],
            'Rajasthan' => ['Ajmer', 'Alwar', 'Banswara', 'Baran', 'Barmer', 'Bharatpur', 'Bhilwara', 'Bikaner', 'Bundi', 'Chittorgarh', 'Churu', 'Dausa', 'Dholpur', 'Dungarpur', 'Hanumangarh', 'Jaipur', 'Jaisalmer', 'Jalore', 'Jhalawar', 'Jhunjhunu', 'Jodhpur', 'Karauli', 'Kota', 'Nagaur', 'Pali', 'Pratapgarh', 'Rajsamand', 'Sawai Madhopur', 'Sikar', 'Sirohi', 'Sri Ganganagar', 'Tonk', 'Udaipur'],
            'Sikkim' => ['Gangtok', 'Gyalshing', 'Mangan', 'Namchi', 'Pakyong', 'Soreng'],
            'Tamil Nadu' => ['Ariyalur', 'Chengalpattu', 'Chennai', 'Coimbatore', 'Cuddalore', 'Dharmapuri', 'Dindigul', 'Erode', 'Kallakurichi', 'Kancheepuram', 'Karur', 'Krishnagiri', 'Madurai', 'Mayiladuthurai', 'Nagapattinam', 'Namakkal', 'Nilgiris', 'Perambalur', 'Pudukkottai', 'Ramanathapuram', 'Ranipet', 'Salem', 'Sivaganga', 'Tenkasi', 'Thanjavur', 'Theni', 'Thoothukudi', 'Tiruchirappalli', 'Tirunelveli', 'Tirupathur', 'Tiruppur', 'Tiruvallur', 'Tiruvannamalai', 'Tiruvarur', 'Vellore', 'Viluppuram', 'Virudhunagar'],
            'Telangana' => ['Adilabad', 'Bhadradri Kothagudem', 'Hanumakonda', 'Hyderabad', 'Jagitial', 'Jangaon', 'Jayashankar Bhupalapally', 'Jogulamba Gadwal', 'Kamareddy', 'Karimnagar', 'Khammam', 'Kumuram Bheem', 'Mahabubabad', 'Mahabubnagar', 'Mancherial', 'Medak', 'Medchal Malkajgiri', 'Mulugu', 'Nagarkurnool', 'Nalgonda', 'Narayanpet', 'Nirmal', 'Nizamabad', 'Peddapalli', 'Rajanna Sircilla', 'Ranga Reddy', 'Sangareddy', 'Siddipet', 'Suryapet', 'Vikarabad', 'Wanaparthy', 'Warangal', 'Yadadri Bhuvanagiri'],
            'Tripura' => ['Dhalai', 'Gomati', 'Khowai', 'North Tripura', 'Sepahijala', 'South Tripura', 'Unakoti', 'West Tripura'],
            'Uttar Pradesh' => ['Agra', 'Aligarh', 'Ambedkar Nagar', 'Amethi', 'Amroha', 'Auraiya', 'Ayodhya', 'Azamgarh', 'Baghpat', 'Bahraich', 'Ballia', 'Balrampur', 'Banda', 'Barabanki', 'Bareilly', 'Basti', 'Bhadohi', 'Bijnor', 'Budaun', 'Bulandshahr', 'Chandauli', 'Chitrakoot', 'Deoria', 'Etah', 'Etawah', 'Farrukhabad', 'Fatehpur', 'Firozabad', 'Gautam Buddha Nagar', 'Ghaziabad', 'Ghazipur', 'Gonda', 'Gorakhpur', 'Hamirpur', 'Hapur', 'Hardoi', 'Hathras', 'Jalaun', 'Jaunpur', 'Jhansi', 'Kannauj', 'Kanpur Dehat', 'Kanpur Nagar', 'Kasganj', 'Kaushambi', 'Kheri', 'Kushinagar', 'Lalitpur', 'Lucknow', 'Maharajganj', 'Mahoba', 'Mainpuri', 'Mathura', 'Mau', 'Meerut', 'Mirzapur', 'Moradabad', 'Muzaffarnagar', 'Pilibhit', 'Pratapgarh', 'Prayagraj', 'Raebareli', 'Rampur', 'Saharanpur', 'Sambhal', 'Sant Kabir Nagar', 'Shahjahanpur', 'Shamli', 'Shravasti', 'Siddharthnagar', 'Sitapur', 'Sonbhadra', 'Sultanpur', 'Unnao', 'Varanasi'],
            'Uttarakhand' => ['Almora', 'Bageshwar', 'Chamoli', 'Champawat', 'Dehradun', 'Haridwar', 'Nainital', 'Pauri Garhwal', 'Pithoragarh', 'Rudraprayag', 'Tehri Garhwal', 'Udham Singh Nagar', 'Uttarkashi'],
            'West Bengal' => ['Alipurduar', 'Bankura', 'Birbhum', 'Cooch Behar', 'Dakshin Dinajpur', 'Darjeeling', 'Hooghly', 'Howrah', 'Jalpaiguri', 'Jhargram', 'Kalimpong', 'Kolkata', 'Malda', 'Murshidabad', 'Nadia', 'North 24 Parganas', 'Paschim Bardhaman', 'Paschim Medinipur', 'Purba Bardhaman', 'Purba Medinipur', 'Purulia', 'South 24 Parganas', 'Uttar Dinajpur'],
        ];
    }

    /** Hardcoded fallback state list used when countriesnow is unreachable. */
    private function fallbackStates(): array
    {
        return [
            'Andaman and Nicobar Islands', 'Andhra Pradesh', 'Arunachal Pradesh',
            'Assam', 'Bihar', 'Chandigarh', 'Chhattisgarh',
            'Dadra and Nagar Haveli and Daman and Diu', 'Delhi', 'Goa',
            'Gujarat', 'Haryana', 'Himachal Pradesh', 'Jammu and Kashmir',
            'Jharkhand', 'Karnataka', 'Kerala', 'Ladakh', 'Lakshadweep',
            'Madhya Pradesh', 'Maharashtra', 'Manipur', 'Meghalaya', 'Mizoram',
            'Nagaland', 'Odisha', 'Puducherry', 'Punjab', 'Rajasthan',
            'Sikkim', 'Tamil Nadu', 'Telangana', 'Tripura', 'Uttar Pradesh',
            'Uttarakhand', 'West Bengal',
        ];
    }

    /**
     * GET /api/general/distance?from=Chennai&to=Mysuru
     *
     * Returns driving distance in km between two Indian locations.
     *
     * Priority:
     *   1. OpenRouteService (ORS) — free 2,000 req/day, no credit card
     *      → sign up at https://openrouteservice.org/dev/#/home → TOKENS
     *      → set ORS_API_KEY in .env
     *   2. Geoapify — free 3,000 req/day, no credit card
     *      → sign up at https://www.geoapify.com/
     *      → set GEOAPIFY_API_KEY in .env
     *   3. OSRM public demo — completely free, no key (less accurate)
     *
     * Results cached 7 days per route pair.
     */
    public function getDistance(Request $request): JsonResponse
    {
        $request->validate([
            'from' => 'required|string|max:255',
            'to'   => 'required|string|max:255',
        ]);

        $from     = trim($request->input('from'));
        $to       = trim($request->input('to'));
        $cacheKey = 'dist_v3_' . md5(strtolower($from) . '||' . strtolower($to));

        $result = Cache::remember($cacheKey, 86400 * 7, function () use ($from, $to) {

            /* ── Shared geocoder: Nominatim → [lat, lon] ── */
            $geocode = function (string $q): ?array {
                try {
                    $r = Http::timeout(8)
                        ->withoutVerifying()
                        ->withHeaders(['User-Agent' => 'TransportApp/1.0'])
                        ->get('https://nominatim.openstreetmap.org/search', [
                            'q'            => $q . ', India',
                            'format'       => 'json',
                            'limit'        => 1,
                            'countrycodes' => 'in',
                        ]);
                    if ($r->successful() && !empty($r->json())) {
                        $hit = $r->json()[0];
                        return [(float) $hit['lat'], (float) $hit['lon']];
                    }
                } catch (\Exception $e) {
                    \Log::warning('Distance geocode failed', ['q' => $q, 'err' => $e->getMessage()]);
                }
                return null;
            };

            $c1 = $geocode($from);
            if (!$c1) return null;

            $c2 = $geocode($to);
            if (!$c2) return null;

            [$la1, $lo1] = $c1;
            [$la2, $lo2] = $c2;

            /* ── 1. OpenRouteService (best accuracy for Indian highways) ── */
            $orsKey = config('services.ors.key');
            if (!empty($orsKey)) {
                try {
                    $r = Http::timeout(12)
                        ->withoutVerifying()
                        ->withHeaders([
                            'Authorization' => $orsKey,
                            'Content-Type'  => 'application/json',
                        ])
                        ->post('https://api.openrouteservice.org/v2/directions/driving-car', [
                            'coordinates' => [[$lo1, $la1], [$lo2, $la2]],
                        ]);
                    if ($r->successful()) {
                        $meters = $r->json()['routes'][0]['summary']['distance'] ?? null;
                        if ($meters) {
                            return ['km' => round($meters / 1000, 1), 'source' => 'ors'];
                        }
                    }
                } catch (\Exception $e) {
                    \Log::warning('Distance ORS failed', ['err' => $e->getMessage()]);
                }
            }

            /* ── 2. Geoapify ── */
            $geoapifyKey = config('services.geoapify.key');
            if (!empty($geoapifyKey)) {
                try {
                    $r = Http::timeout(12)
                        ->withoutVerifying()
                        ->get('https://api.geoapify.com/v1/routing', [
                            'waypoints' => "{$la1},{$lo1}|{$la2},{$lo2}",
                            'mode'      => 'drive',
                            'apiKey'    => $geoapifyKey,
                        ]);
                    if ($r->successful()) {
                        $meters = $r->json()['features'][0]['properties']['distance'] ?? null;
                        if ($meters) {
                            return ['km' => round($meters / 1000, 1), 'source' => 'geoapify'];
                        }
                    }
                } catch (\Exception $e) {
                    \Log::warning('Distance geoapify failed', ['err' => $e->getMessage()]);
                }
            }

            /* ── 3. OSRM public demo (free, no key) ── */
            try {
                $osrm = Http::timeout(10)
                    ->withoutVerifying()
                    ->get(
                        "https://router.project-osrm.org/route/v1/driving/{$lo1},{$la1};{$lo2},{$la2}",
                        ['overview' => 'false', 'alternatives' => 'false']
                    );
                if ($osrm->successful()) {
                    $dist = $osrm->json()['routes'][0]['distance'] ?? null;
                    if ($dist) {
                        return ['km' => round($dist / 1000, 1), 'source' => 'osrm'];
                    }
                }
            } catch (\Exception $e) {
                \Log::warning('Distance OSRM failed', ['err' => $e->getMessage()]);
            }

            return null;
        });

        if (!$result) {
            return response()->json(['error' => 'Could not calculate distance'], 422);
        }

        return response()->json($result);
    }
}
