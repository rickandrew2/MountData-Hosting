<?php
header('Content-Type: application/json');

// Get the user message
$data = json_decode(file_get_contents('php://input'), true);
$userMessage = strtolower(trim($data['message']));

// Improved response logic with question variations and keywords
$responses = [
    // Account-related queries
    'account' => [
        'keywords' => [
            'account', 'sign up', 'signup', 'register', 'login', 'create account',
            'how to join', 'become member', 'make account', 'registration', 'sign in',
            'cant login', "can't login", 'forgot password', 'reset password', 'change password',
            'new account', 'profile', 'my account'
        ],
        'response' => "<div class='chat-section'>
            <div class='chat-section-title'>Managing Your Account:</div>
            <ul class='chat-list'>
                <li>Create Account: Click 'Login' → 'Sign Up'</li>
                <li>Login: Enter your credentials</li>
                <li>Reset Password: Click 'Forgot Password'</li>
                <li>Update Profile: Access via profile picture</li>
            </ul>
            <div class='chat-tip'>Need help? Contact support@mountdata.com</div>
        </div>"
    ],
    
    // Map-related queries
    'map' => [
        'keywords' => [
            'map', 'location', 'find mountain', 'search mountain', 'where', 'navigate',
            'directions', 'how to get there', 'mountain location', 'nearby mountains',
            'closest mountain', 'search area', 'find location', 'show map', 'view map',
            'distance', 'how far', 'route to', 'way to', 'locate', 'navigation'
        ],
        'response' => "<div class='chat-section'>
            <div class='chat-section-title'>Our interactive map features are easy to use:</div>
            <ul class='chat-list'>
                <li>Click 'Explore' → 'Maps' in the main menu</li>
                <li>Use the search bar to find specific mountains</li>
                <li>Filter mountains by height, difficulty, or region</li>
                <li>Click on any mountain for detailed information</li>
                <li>Save locations to your favorites</li>
                <li>Get driving directions to trailheads</li>
            </ul>
            <div class='chat-tip'>Pro tip: You can also download maps for offline use! 🗺️</div>
        </div>"
    ],
    
    // About MountData
    'about' => [
        'keywords' => [
            'mountdata', 'what is', 'about', 'tell me about', 'website', 'purpose',
            'who are you', 'company', 'platform', 'service', 'what do you do',
            'how does this work', 'explain', 'help me understand', 'features',
            'what can i do here', 'benefits', 'introduction', 'tell me more',
            'blog', 'articles', 'sustainability', 'sdg', 'conservation'
        ],
        'response' => "<div class='chat-section'>
            <div class='chat-section-title'>Welcome to MountData! 🏔️</div>
            <div class='chat-section'>
                <div class='chat-section-title'>Our Features:</div>
                <ul class='chat-list'>
                    <li>Comprehensive database of mountains and trails</li>
                    <li>Real-time weather updates</li>
                    <li>Trail difficulty ratings and reviews</li>
                    <li>Community of mountain enthusiasts</li>
                    <li>Safety guides and tips</li>
                    <li>Interactive maps and route planning</li>
                    <li>Photo sharing and trip reports</li>
                </ul>
            </div>
            <div class='chat-section'>
                <div class='chat-section-title'>🌿 Conservation & Education:</div>
                <ul class='chat-list'>
                    <li>Educational blogs on mountain ecosystems</li>
                    <li>SDG 15 (Life on Land) focused content:
                        <ul>
                            <li>Mountain biodiversity protection</li>
                            <li>Sustainable hiking practices</li>
                            <li>Conservation success stories</li>
                            <li>Local flora and fauna guides</li>
                        </ul>
                    </li>
                    <li>Community conservation initiatives</li>
                    <li>Environmental impact tracking</li>
                </ul>
            </div>
            <div class='chat-tip'>Our mission is to make mountain exploration safer and more accessible while promoting environmental stewardship and sustainable practices! 🌱</div>
        </div>"
    ],
    
    // Trail information
    'trails' => [
        'keywords' => [
            'trail', 'hiking', 'path', 'route', 'trek', 'difficulty',
            'how hard', 'easy trails', 'beginner', 'advanced', 'expert',
            'walking', 'hiking path', 'trail map', 'trail guide', 'best trails',
            'popular trails', 'recommended', 'which trail', 'trail rating',
            'trail review', 'trail distance', 'elevation', 'climb'
        ],
        'response' => "<div class='chat-section'>
            <div class='chat-section-title'>Trail Information System</div>
            <div class='chat-section'>
                <div class='chat-section-title'>Trail Details:</div>
                <ul class='chat-list'>
                    <li>Detailed trail descriptions</li>
                    <li>Difficulty ratings (Easy 🟢, Moderate 🟡, Hard 🔴)</li>
                    <li>Distance and elevation data</li>
                    <li>User reviews and photos</li>
                    <li>Current trail conditions</li>
                    <li>Estimated completion times</li>
                    <li>Points of interest along the way</li>
                </ul>
            </div>
            <div class='chat-section'>
                <div class='chat-section-title'>Additional Information:</div>
                <ul class='chat-list'>
                    <li>Best seasons to visit</li>
                    <li>Trail facilities</li>
                    <li>Parking information</li>
                    <li>Trail access points</li>
                </ul>
            </div>
            <div class='chat-tip'>Filter trails by difficulty, length, or user ratings to find the perfect hike for you! 🥾</div>
        </div>"
    ],
    
    // Safety information
    'safety' => [
        'keywords' => [
            'safe', 'safety', 'emergency', 'danger', 'precaution', 'prepare',
            'what to bring', 'equipment', 'gear', 'first aid', 'rescue',
            'emergency contact', 'guidelines', 'rules', 'requirements',
            'weather warning', 'avalanche', 'risk', 'dangerous', 'warning',
            'preparation', 'tips', 'advice', 'recommended gear'
        ],
        'response' => "<div class='chat-section'>
            <div class='chat-section-title'>Safety Guidelines</div>
            <div class='chat-section'>
                <div class='chat-section-title'>🎒 Essential Gear:</div>
                <ul class='chat-list'>
                    <li>Navigation tools (map, compass, GPS)</li>
                    <li>First aid kit</li>
                    <li>Plenty of water</li>
                    <li>Emergency shelter</li>
                    <li>Appropriate clothing</li>
                </ul>
            </div>
            <div class='chat-section'>
                <div class='chat-section-title'>⚠️ Safety Tips:</div>
                <ul class='chat-list'>
                    <li>Always check weather before hiking</li>
                    <li>Tell someone your plans</li>
                    <li>Stay on marked trails</li>
                    <li>Carry emergency contacts</li>
                    <li>Know your limits</li>
                </ul>
            </div>
            <div class='chat-section'>
                <div class='chat-section-title'>🆘 Emergency:</div>
                <ul class='chat-list'>
                    <li>Call local emergency services</li>
                    <li>Use our emergency beacon feature</li>
                    <li>Follow the safety protocols in our guide</li>
                </ul>
            </div>
            <div class='chat-tip'>Visit our Safety Guide section for comprehensive information!</div>
        </div>"
    ],
    
    // Weather information
    'weather' => [
        'keywords' => [
            'weather', 'forecast', 'temperature', 'rain', 'condition',
            'climate', 'snow', 'wind', 'sunny', 'cloudy', 'storm',
            'weather report', 'current weather', 'weather update',
            'weather alert', 'weather warning', 'best time', 'season',
            'when to go', 'weather forecast', 'precipitation'
        ],
        'response' => "<div class='chat-section'>
            <div class='chat-section-title'>Weather Information</div>
            <div class='chat-section'>
                <div class='chat-section-title'>🌤️ Our weather features include:</div>
                <ul class='chat-list'>
                    <li>Real-time weather updates</li>
                    <li>7-day forecasts</li>
                    <li>Temperature ranges</li>
                    <li>Precipitation chances</li>
                    <li>Wind conditions</li>
                    <li>Visibility reports</li>
                </ul>
            </div>
            <div class='chat-section'>
                <div class='chat-section-title'>⚠️ Weather Recommendations:</div>
                <ul class='chat-list'>
                    <li>Check conditions 24 hours before hiking</li>
                    <li>Monitor weather changes during your hike</li>
                    <li>Set up weather alerts for your planned dates</li>
                </ul>
            </div>
            <div class='chat-tip'>Remember: Mountain weather can change quickly! Always be prepared for various conditions.</div>
        </div>"
    ],
    
    // Greetings and Farewells
    'greeting' => [
        'keywords' => [
            'hi', 'hello', 'hey', 'good morning', 'good afternoon', 'good evening',
            'help', 'start', 'begin', 'assistance', 'support', 'guide me',
            'bye', 'goodbye', 'thanks', 'thank you', 'see you', 'later'
        ],
        'response' => "<div class='chat-section'>
            <div class='chat-section-title'>Hello! 👋 I'm your MountData assistant.</div>
            <div class='chat-section'>
                <div class='chat-section-title'>I can help you with:</div>
                <ul class='chat-list'>
                    <li>Finding trails and mountains</li>
                    <li>Safety information</li>
                    <li>Weather updates</li>
                    <li>Account management</li>
                    <li>Maps and navigation</li>
                </ul>
            </div>
            <div class='chat-tip'>What would you like to know more about? Feel free to ask any questions! 😊</div>
        </div>"
    ],
    
    // New Website Usage category
    'website_usage' => [
        'keywords' => [
            'how to', 'how do i', 'cant find', "can't find", 'where is', 'tutorial',
            'guide', 'help me', 'confused', 'stuck', 'not working', 'problem with',
            'issue with', 'website', 'browser', 'feature', 'function', 'menu',
            'settings', 'like', 'bookmark', 'report', 'upload', 'photo', 'picture',
            'review', 'comment', 'rate', 'search', 'filter', 'sort', 'find', 'access',
            'mountain profile', 'conditions', 'inquiry', 'community', 'explore'
        ],
        'response' => "<div class='chat-section'>
            <div class='chat-section-title'>Website Navigation Guide 🌐</div>
            <div class='chat-section'>
                <div class='chat-section-title'>Main Features:</div>
                <ul class='chat-list'>
                    <li>Search: Use the search bar at the top 🔍</li>
                    <li>Navigation: Main menu is in the top-left ☰</li>
                    <li>Profile: Access via the top-right icon 👤</li>
                </ul>
            </div>
            <div class='chat-section'>
                <div class='chat-section-title'>Mountain Profiles:</div>
                <ul class='chat-list'>
                    <li>Find detailed information about each mountain</li>
                    <li>View current weather conditions and updates</li>
                    <li>See trail information and difficulty levels</li>
                    <li>Add mountains to your bookmarks</li>
                    <li>Like and review mountains</li>
                    <li>View photos of the mountain and trails</li>
                </ul>
            </div>
            <div class='chat-section'>
                <div class='chat-section-title'>Maps & Exploration:</div>
                <ul class='chat-list'>
                    <li>Access interactive maps via 'Explore' → 'Maps'</li>
                    <li>Search and find mountains in your area</li>
                    <li>View mountain locations and details</li>
                </ul>
            </div>
            <div class='chat-section'>
                <div class='chat-section-title'>Community & Reviews:</div>
                <ul class='chat-list'>
                    <li>View all user reviews in the 'Community' section</li>
                    <li>Share your own mountain experiences</li>
                    <li>Rate mountains you've visited</li>
                </ul>
            </div>
            <div class='chat-section'>
                <div class='chat-section-title'>Need Help?</div>
                <ul class='chat-list'>
                    <li>Submit inquiries through our contact form</li>
                    <li>Report issues or problems</li>
                    <li>Ask questions about specific mountains</li>
                </ul>
            </div>
            <div class='chat-tip'>Looking for something specific? Feel free to ask about any mountain or feature! 😊</div>
        </div>"
    ],

    'thanks' => [
        'keywords' => [
            'thank', 'thanks', 'thank you', 'thankyou', 'appreciate', 'grateful',
            'helped', 'helpful', 'tysm', 'thx', 'ty'
        ],
        'response' => "<div class='chat-section'>
            <div class='chat-section-title'>You're welcome! 😊</div>
            <div class='chat-section'>
                <p>I'm glad I could help! Is there anything else you'd like to know about?</p>
                <ul class='chat-list'>
                    <li>Finding trails and mountains</li>
                    <li>Inquiry submission</li>
                    <li>Safety information</li>
                    <li>Account help</li>
                </ul>
            </div>
            <div class='chat-tip'>Feel free to ask any other questions!</div>
        </div>"
    ]
];

// Add FAQ-specific responses
$faq_responses = [
    'how do i create an account?' => [
        'response' => "<div class='chat-section'>
            <div class='chat-section-title'>Creating an Account:</div>
            <ol class='chat-list'>
                <li>Click the 'Login' button in the top right</li>
                <li>Select 'Sign Up' option</li>
                <li>Fill in your details:
                    <ul>
                        <li>Full name</li>
                        <li>Email address</li>
                        <li>Password</li>
                    </ul>
                </li>
                <li>Verify your email</li>
                <li>Complete your profile</li>
            </ol>
            <div class='chat-tip'>Need help? Contact support@mountdata.com</div>
        </div>",
        'showFAQ' => true
    ],
    
    'how do i use the map?' => [
        'response' => "<div class='chat-section'>
            <div class='chat-section-title'>Using the Map:</div>
            <ol class='chat-list'>
                <li>Click 'Explore' → 'Maps' in the navigation</li>
                <li>Click on a mountain container on the map on the left column and it will show you its location in the map</li>
                <li>Filter mountains by:
                    <ul>
                        <li>Difficulty level</li>
                        <li>Elevation</li>
                        <li>Location</li>
                    </ul>
                </li>
                <li>Interactive features:
                    <ul>
                        <li>Click the travel icon to go to the mountain profile</li>
                        <li>Drag the human icon (Pegman) onto any highlighted area to enter Street View/3D mode</li>
                        <li>Use mouse or touch controls to rotate and explore the 360° view</li>
                        <li>Blue lines on the map indicate available Street View coverage</li>
                    </ul>
                </li>
            </ol>
            <div class='chat-tip'>Pro tips: 
                <ul>
                    <li>Use filters to find mountains that match your experience level! 🏔️</li>
                    <li>Street View helps you preview trail conditions and parking areas before your visit! 👀</li>
                </ul>
            </div>
        </div>",
        'showFAQ' => true
    ],
    
    'tell me about safety guidelines' => [
        'response' => "<div class='chat-section'>
            <div class='chat-section-title'>Safety Guidelines:</div>
            <ul class='chat-list'>
                <li>Essential Equipment:
                    <ul>
                        <li>Navigation tools</li>
                        <li>First aid kit</li>
                        <li>Emergency shelter</li>
                        <li>Appropriate clothing</li>
                    </ul>
                </li>
                <li>Before Hiking:
                    <ul>
                        <li>Check weather forecast</li>
                        <li>Inform someone of your plans</li>
                        <li>Study your route</li>
                    </ul>
                </li>
                <li>During the Hike:
                    <ul>
                        <li>Stay on marked trails</li>
                        <li>Monitor weather changes</li>
                        <li>Carry emergency contacts</li>
                        <li>Know your limits</li>
                    </ul>
                </li>
                <li>After the Hike:
                    <ul>
                        <li>Check for injuries and hazards</li>
                        <li>Rest and recover</li>
                        <li>Share your experience</li>
                    </ul>
                </li>
            </ul>
            <div class='chat-tip'>Need help? Contact support@mountdata.com</div>
        </div>",
        'showFAQ' => true
    ],
    
    'how do i submit an inquiry?' => [
        'response' => '<div class="chat-section">
            <div class="chat-section-title">Submitting an Inquiry:</div>
            <ol class="chat-list">
                <li>Navigate to \'Contact Us\' in the footer menu</li>
                <li>Fill out the inquiry form:
                    <ul>
                        <li>Your name</li>
                        <li>Email address</li>
                        <li>Subject of inquiry</li>
                        <li>Detailed message</li>
                    </ul>
                </li>
                <li>Click \'Submit Inquiry\'</li>
                <li>Our team typically responds within 24-48 hours</li>
            </ol>
            <div class="chat-tip">For urgent matters, please contact us directly at support@mountdata.com</div>
        </div>',
        'showFAQ' => true
    ],
    
    'how do i view mountain profiles?' => [
        'response' => "<div class='chat-section'>
            <div class='chat-section-title'>Viewing Mountain Profiles:</div>
            <ol class='chat-list'>
                <li>Access mountain profiles in three ways:
                    <ul>
                        <li>Use the search bar at the top</li>
                        <li>Browse the 'Mountains' section</li>
                        <li>Click mountain containers on the map page</li>
                    </ul>
                </li>
                <li>Each profile includes:
                    <ul>
                        <li>Elevation and difficulty rating</li>
                        <li>Current weather conditions</li>
                        <li>Trail options and routes</li>
                        <li>User reviews and photos</li>
                        <li>Emergency information</li>
                    </ul>
                </li>
                <li>Interactive features:
                    <ul>
                        <li>Save to favorites or Bookmarks</li>
                        <li>View 3D terrain</li>
                    </ul>
                </li>
            </ol>
            <div class='chat-tip'>Pro tip: Login to access additional features like saving favorites and writing reviews! 🏔️</div>
        </div>",
        'showFAQ' => true
    ]
];

// Function to calculate similarity between two strings
function similarity($str1, $str2) {
    $str1 = strtolower($str1);
    $str2 = strtolower($str2);
    
    // Calculate Levenshtein distance
    $distance = levenshtein($str1, $str2);
    $maxLength = max(strlen($str1), strlen($str2));
    
    // Return similarity percentage
    return (1 - $distance / $maxLength) * 100;
}

// Find the best matching response
$bestMatch = null;
$highestScore = 0;

// Check FAQ responses first
foreach ($faq_responses as $question => $data) {
    if (similarity($userMessage, $question) > 80) {
        $bestMatch = $data['response'];
        break;
    }
}

// Only check general responses if no FAQ match was found
if (!$bestMatch) {
    foreach ($responses as $category => $data) {
        foreach ($data['keywords'] as $keyword) {
            // Check for exact matches in the message
            if (strpos($userMessage, $keyword) !== false) {
                $bestMatch = $data['response'];
                break 2;
            }
            
            // Check each word in the user message against keywords
            $words = explode(' ', $userMessage);
            foreach ($words as $word) {
                $similarityScore = similarity($word, $keyword);
                if ($similarityScore > 80 && $similarityScore > $highestScore) {
                    $highestScore = $similarityScore;
                    $bestMatch = $data['response'];
                }
            }
        }
    }
}

// Default response if no good match is found
if (!$bestMatch) {
    $bestMatch = "<div class='chat-section'>
        <div class='chat-section-title'>I'm not quite sure about that. 🤔</div>
        <div class='chat-section'>
            <div class='chat-section-title'>You can ask me about:</div>
            <ul class='chat-list'>
                <li>Creating an account</li>
                <li>Using the interactive map</li>
                <li>Finding trails and mountains</li>
                <li>Safety guidelines and tips</li>
                <li>Weather conditions and forecasts</li>
                <li>General information about MountData</li>
            </ul>
        </div>
        <div class='chat-tip'>Please try rephrasing your question, and I'll do my best to help! 😊</div>
    </div>";
}

echo json_encode(['response' => $bestMatch]); 