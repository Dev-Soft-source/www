@extends('layouts.template')

@section('content')

<div class="container mx-auto my-6 md:my-10 xl:my-14 px-4 xl:px-0 pt-safe max-w-4xl">
    <div class="flex justify-center pb-4 pt-4 md:pt-2 hideheader1">
        <h1 class="mb-4 text-center font-FuturaMdCnBT text-primary">
            ProximaRide's Cost-Sharing Compliance Policy
        </h1>
    </div>

    <div class="prose prose-gray max-w-none pb-8 space-y-3 text-gray-800">
        <p class="leading-relaxed">
            ProximaRide is a cost-sharing platform, not a transportation service. Drivers may only post rides for trips they are already planning to make for personal reasons, and may only request contributions that help share trip-related costs (such as fuel, tolls, parking, and reasonable vehicle expenses). Earning a profit or operating as a transportation service through the platform is strictly prohibited.
        </p>
        <p class="leading-relaxed">
            To help maintain a lawful, safe, and fair ridesharing environment, ProximaRide may review ride activity patterns such as trip frequency, route repetition, timing, turnaround intervals, seat availability, pricing behaviour, and other indicators of potential commercial or for-profit use. Where activity appears inconsistent with cost-sharing, we may contact the driver for clarification, temporarily restrict features, or suspend the account pending review.
        </p>
        <p class="leading-relaxed">
            Rides with existing booked passengers are handled carefully to minimize disruption. However, ProximaRide may cancel future rides or suspend accounts where misuse is confirmed or highly likely. Failure to cooperate with a compliance review may also result in suspension.
        </p>
        <p class="leading-relaxed">
            By using ProximaRide, drivers agree to follow this policy and our Terms &amp; Conditions, and to use the platform only for genuine cost-sharing between private individuals.
        </p>

        <h3 class="font-FuturaMdCnBT text-xl md:text-2xl pt-6 text-primary">1. Purpose of this Policy</h2>
        <p class="leading-relaxed">
            ProximaRide is a <strong>cost-sharing carpooling platform</strong>, not a taxi, ride-hail, or commercial transportation service. The purpose of this Policy is to clearly define what <strong>cost-sharing means on ProximaRide</strong>, how it must be applied by Drivers and Passengers, and the rules that ensure all Trips remain <strong>non-commercial, compliant, and lawful</strong>.
        </p>
        <p class="leading-relaxed">
            This Policy forms part of the <strong>ProximaRide Terms of Service</strong> and applies to all Members. By using ProximaRide, you agree to follow this Policy strictly and in good faith.
        </p>

        <h3 class="font-FuturaMdCnBT text-xl md:text-2xl pt-6 text-primary">2. What Cost-Sharing Means on ProximaRide</h3>
        <p class="leading-relaxed">
            On ProximaRide, a Trip is based on the principle that:
        </p>
        <ul class="list-disc pl-6 space-y-2 text-base">
            <li>The <strong>Passenger helps the Driver cover a fair portion of the operating costs of a Trip that the Driver was already planning to make</strong>, without generating profit for the Driver.</li>
            <li>The <strong>Booking Price</strong> (called "Contribution" in some jurisdictions) represents a <strong>cost-sharing amount</strong>, not a fare, wage, or commercial payment. It is intended solely to offset reasonable expenses related to the Trip.</li>
        </ul>
        <p class="leading-relaxed mt-4">
            Cost-sharing applies only when:
        </p>
        <ul class="list-disc pl-6 space-y-2 text-base">
            <li>The Driver is already making the Trip for their own personal purpose</li>
            <li>The Passenger joins the Trip to share the ride</li>
            <li>The Booking Price is limited to <strong>cost-recovery only</strong></li>
        </ul>

        <h3 class="font-FuturaMdCnBT text-xl md:text-2xl pt-6 text-primary">3. Costs That May Be Shared</h3>
        <p class="leading-relaxed">
            The Booking Price may reasonably reflect a portion of:
        </p>
        <ul class="list-disc pl-6 space-y-2 text-base">
            <li class="font-FuturaMdCnBT">Fuel</li>
            <li>Highway tolls</li>
            <li class="font-FuturaMdCnBT">Parking fees during the trip</li>
            <li class="font-FuturaMdCnBT">Wear-and-tear as normally recognized in carpool cost-sharing models</li>
            <li class="font-FuturaMdCnBT">Reasonable trip-related vehicle expenses</li>
        </ul>
        <p class="leading-relaxed mt-4">
            The Booking Price is not wages or revenue. It is not compensation for time, effort, or driving service.
        </p>

        <h3 class="font-FuturaMdCnBT text-xl md:text-2xl pt-6 text-primary">4. Costs That May NOT Be Included</h3>
        <p class="leading-relaxed">
            The Booking Price must <strong>not</strong> include or represent:
        </p>
        <ul class="list-disc pl-6 space-y-2 text-base">
            <li>Profit or revenue of any kind</li>
            <li>Compensation for labor or time</li>
            <li>Business or commercial operating costs</li>
            <li>Mark-ups or arbitrary price increases</li>
            <li>Vehicle financing costs, insurance premiums, or repairs unrelated to the Trip</li>
            <li>Tips, bonuses, or incentives</li>
            <li>Any payment that treats the Driver as a professional operator</li>
        </ul>
        <p class="leading-relaxed mt-4">
            If the intent or effect of pricing is to <strong>earn money</strong>, the Trip is no longer cost-sharing and is <strong>not permitted on ProximaRide</strong>.
        </p>

        <h3 class="font-FuturaMdCnBT text-xl md:text-2xl pt-6 text-primary">5. ProximaRide is a Cost-Sharing Platform — Not a Transportation Service</h3>
        <p class="leading-relaxed">
            ProximaRide does <strong>not</strong>:
        </p>
        <ul class="list-disc pl-6 space-y-2 text-base">
            <li>Provide transportation services</li>
            <li>Employ or contract Drivers</li>
            <li>Set Trip purposes</li>
            <li>Control vehicle operation</li>
            <li>Act as an insurer, broker, or carrier</li>
        </ul>
        <p class="leading-relaxed mt-4">
            Drivers and Passengers travel <strong>as private individuals sharing a Trip</strong>.
        </p>
        <p class="leading-relaxed">
            The Driver remains fully responsible for:
        </p>
        <ul class="list-disc pl-6 space-y-2 text-base">
            <li>Deciding whether to accept a Passenger</li>
            <li>Driving safely and lawfully</li>
            <li>Ensuring the vehicle is authorized and insured</li>
        </ul>
        <p class="leading-relaxed mt-4">
            Passengers understand that they are <strong>sharing a ride</strong>, not purchasing a transport service.
        </p>

        <h3 class="font-FuturaMdCnBT text-xl md:text-2xl pt-6 text-primary">6. Rules Drivers Must Follow to Remain Cost-Sharing Compliant</h3>
        <p class="leading-relaxed">
            To comply with this Policy, Drivers must:
        </p>
        <ul class="list-disc pl-6 space-y-2 text-base">
            <li>Offer Trips they were already planning to make</li>
            <li>Avoid posting Trips for the sole purpose of earning money</li>
            <li>Set Booking Prices that remain <strong>reasonable and cost-based</strong></li>
            <li>Not operate like a taxi, shuttle, or professional driver</li>
            <li>Avoid frequent or systematic trips that resemble commercial activity</li>
            <li>Not solicit off-platform cash payments</li>
            <li>Not charge different prices based on negotiation or demand</li>
        </ul>
        <p class="leading-relaxed mt-4">
            ProximaRide may request clarification or documentation if pricing or behavior suggests a <strong>commercial intent</strong>.
        </p>

        <h3 class="font-FuturaMdCnBT text-xl md:text-2xl pt-6 text-primary">7. Rules Passengers Must Follow</h3>
        <p class="leading-relaxed">
            Passengers must:
        </p>
        <ul class="list-disc pl-6 space-y-2 text-base">
            <li>Understand that the Booking Price is a <strong>contribution to Trip costs</strong></li>
            <li>Not attempt to negotiate the price as if purchasing a transport service</li>
            <li>Treat the Trip as carpooling between private individuals</li>
            <li>Not request "taxi-style" behavior such as custom detours for payment</li>
        </ul>
        <p class="leading-relaxed mt-4">
            Passengers who pressure Drivers into commercial behavior may be suspended.
        </p>

        <h3 class="font-FuturaMdCnBT text-xl md:text-2xl pt-6 text-primary">8. Pricing Transparency and Platform Safeguards</h3>
        <p class="leading-relaxed">
            To support cost-sharing compliance, ProximaRide may:
        </p>
        <ul class="list-disc pl-6 space-y-2 text-base">
            <li>Provide recommended pricing guidance</li>
            <li>Prevent unusually high or profit-oriented pricing</li>
            <li>Review histories of frequent or repetitive Trips</li>
            <li>Investigate reports of commercial-style behavior</li>
            <li>Suspend or remove users who violate cost-sharing rules</li>
        </ul>
        <p class="leading-relaxed mt-4">
            ProximaRide reserves the right to adjust policies to remain compliant with applicable regulations.
        </p>

        <h3 class="font-FuturaMdCnBT text-xl md:text-2xl pt-6 text-primary">9. Signs of Non-Compliant / Commercial Activity</h3>
        <p class="leading-relaxed">
            Trips may be investigated or removed if they show indicators such as:
        </p>
        <ul class="list-disc pl-6 space-y-2 text-base">
            <li>Pricing clearly exceeding reasonable trip costs</li>
            <li>Multiple daily or repeated intercity routes similar to taxi services</li>
            <li>Driver operating trips solely because of passenger demand</li>
            <li>Frequent detours performed for payment</li>
            <li>The Driver advertising themselves as providing transportation services</li>
            <li>Requests for payment outside the platform</li>
        </ul>
        <p class="leading-relaxed mt-4">
            Where behavior appears commercial, ProximaRide may <strong>terminate the account</strong>.
        </p>

        <h3 class="font-FuturaMdCnBT text-xl md:text-2xl pt-6 text-primary">10. Insurance &amp; Legal Responsibility Reminder</h3>
        <p class="leading-relaxed">
            Cost-sharing does not replace or modify:
        </p>
        <ul class="list-disc pl-6 space-y-2 text-base">
            <li>Vehicle insurance obligations</li>
            <li>Licensing requirements</li>
            <li>Road safety laws</li>
        </ul>
        <p class="leading-relaxed mt-4">
            Drivers must ensure their insurance permits <strong>ridesharing or passenger presence</strong>. Members are responsible for confirming their own legal compliance.
        </p>

        <h3 class="font-FuturaMdCnBT text-xl md:text-2xl pt-6 text-primary">11. Reporting &amp; Enforcement</h3>
        <p class="leading-relaxed">
            Members may report suspected non-compliant activity. ProximaRide may:
        </p>
        <ul class="list-disc pl-6 space-y-2 text-base">
            <li>Review Trip records</li>
            <li>Suspend or terminate accounts</li>
            <li>Withhold payouts pending investigation</li>
            <li>Cooperate with lawful regulatory inquiries where applicable</li>
        </ul>
        <p class="leading-relaxed mt-4">
            Repeated or intentional violations may result in <strong>permanent removal from the platform</strong>.
        </p>

        <h3 class="font-FuturaMdCnBT text-xl md:text-2xl pt-6 text-primary">12. Confirmation of Understanding</h3>
        <p class="leading-relaxed">
            By using ProximaRide, all Members agree that:
        </p>
        <ul class="list-disc pl-6 space-y-2 text-base">
            <li>Trips are <strong>non-commercial, cost-sharing activities</strong></li>
            <li>The Booking Price is a <strong>contribution toward shared trip costs</strong></li>
            <li>The platform may enforce this Policy to maintain compliance</li>
        </ul>
        <p class="leading-relaxed mt-4">
            Members who do not agree must not use the platform.
        </p>

        <h3 class="font-FuturaMdCnBT text-xl md:text-2xl pt-6 text-primary">13. Quebec-Specific Cost-Sharing Note</h3>
        <p class="leading-relaxed">
            If you use ProximaRide to post or book Trips within Quebec, please note the following requirements under provincial law:
        </p>
        <p class="leading-relaxed mt-4"><strong>Incidental Travel Requirement: </strong> All Trips must be incidental to the Driver's own planned travel. Drivers cannot operate as commercial transport providers; cost-sharing is only allowed for trips they would take regardless of passengers.</p>
        <p class="leading-relaxed mt-4 "><strong>Cost-Sharing Limit:</strong> The total Booking Price (Passenger contribution toward a Trip) must not exceed the reasonable share of actual expenses. Current regulatory guidance sets a reference of $0.54 per kilometer, though exact amounts may vary based on the distance and associated costs.</p>
        <p class="leading-relaxed mt-4 "><strong>Non-Profit Principle:</strong> Drivers may only recover a fair share of expenses. Booking Prices must not generate profit.</p>
        <p class="leading-relaxed mt-4 "><strong>Trip Frequency and Duration Limits:</strong> Certain limits may apply to the number and total distance of Trips a Driver may offer in Quebec. Drivers should comply with these rules to ensure all carpooling remains legal.</p>
        <p class="leading-relaxed mt-4 "><strong>Platform Compliance:</strong> ProximaRide operates as a cost-sharing platform only. We are not a taxi, ride-hailing service, or commercial transport provider. Members must follow the cost-sharing rules to remain in good standing on the platform.</p>
        <p class="leading-relaxed mt-6">
            By proceeding to post or book a Trip in Quebec, you acknowledge that you are responsible for ensuring that your Booking Prices comply with the above rules. Failure to follow these principles may result in account suspension, Trip cancellation, or other actions deemed necessary to comply with applicable laws.
        </p>
    </div>
</div>

@endsection
