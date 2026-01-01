-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Jun 27, 2025 at 05:28 PM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `meditrack`
--

-- --------------------------------------------------------

--
-- Table structure for table `categories`
--

CREATE TABLE `categories` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `categories`
--

INSERT INTO `categories` (`id`, `name`) VALUES
(1, 'Analgesics'),
(2, 'Antibiotics'),
(3, 'Antidepressants'),
(4, 'Antidiabetics'),
(5, 'Antiepileptics'),
(6, 'Antipsychotics'),
(7, 'Antivirals'),
(8, 'Anticoagulants'),
(9, 'Antihistamines');

-- --------------------------------------------------------

--
-- Table structure for table `medicines`
--

CREATE TABLE `medicines` (
  `medicine_id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `brand` varchar(100) DEFAULT NULL,
  `description` text DEFAULT NULL,
  `quantity` int(11) DEFAULT 0,
  `unit_price` decimal(10,2) DEFAULT NULL,
  `expiry_date` date DEFAULT NULL,
  `added_on` timestamp NOT NULL DEFAULT current_timestamp(),
  `category_id` int(11) DEFAULT NULL,
  `supplier_id` int(11) DEFAULT NULL,
  `img` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `medicines`
--

INSERT INTO `medicines` (`medicine_id`, `name`, `brand`, `description`, `quantity`, `unit_price`, `expiry_date`, `added_on`, `category_id`, `supplier_id`, `img`) VALUES
(1, 'Ibuprofen', 'Advil', 'Nonsteroidal anti-inflammatory drug (NSAID)', 10, 5.99, '2026-12-31', '2025-06-10 18:04:38', 1, NULL, 'Ibruprofen.png'),
(2, 'Acetaminophen (Paracetamol)', 'Tylenol', 'Pain reliever and fever reducer', 10, 4.50, '2025-06-30', '2025-06-10 18:04:38', 1, NULL, 'Acetaminophen.png'),
(3, 'Aspirin', 'Bayer', 'Used for pain, fever, and inflammation', 100, 3.99, '2025-10-15', '2025-06-10 18:04:38', 1, NULL, 'Aspirin.png'),
(4, 'Naproxen', 'Aleve', 'NSAID for inflammation and pain', 80, 6.25, '2027-01-01', '2025-06-10 18:04:38', 1, NULL, 'Naproxen.png'),
(5, 'Diclofenac', 'Voltaren', 'NSAID used to treat pain and inflammation', 70, 7.00, '2026-07-20', '2025-06-10 18:04:38', 1, NULL, 'Diclofenac.png'),
(6, 'Tramadol', 'Ultram', 'Opioid pain medication', 59, 12.99, '2026-06-30', '2025-06-10 18:04:38', 1, NULL, 'Tramadol.png'),
(7, 'Morphine', 'MS Contin', 'Strong opioid painkiller', 50, 15.00, '2025-12-31', '2025-06-10 18:04:38', 1, NULL, 'Morphine.png'),
(8, 'Codeine', 'Tylenol #3', 'Mild to moderate pain relief', 89, 8.75, '2025-09-15', '2025-06-10 18:04:38', 1, NULL, 'Codeine.png'),
(9, 'Ketorolac', 'Toradol', 'Short-term pain management', 74, 9.20, '2026-08-10', '2025-06-10 18:04:38', 1, NULL, 'Ketorolac.png'),
(10, 'Penicillin', 'Pfizerpen', 'Antibiotic used for bacterial infections', 120, 4.80, '2026-12-31', '2025-06-10 18:04:38', 2, NULL, 'Penicillin.png'),
(11, 'Amoxicillin', 'Amoxil', 'Broad-spectrum penicillin antibiotic', 100, 5.50, '2026-10-01', '2025-06-10 18:04:38', 2, NULL, 'Amoxicillin.png'),
(12, 'Ciprofloxacin', 'Cipro', 'Fluoroquinolone antibiotic', 80, 6.20, '2025-11-30', '2025-06-10 18:04:38', 2, NULL, 'Ciprofloxacin.png'),
(13, 'Doxycycline', 'Vibramycin', 'Tetracycline antibiotic', 85, 6.75, '2026-05-15', '2025-06-10 18:04:38', 2, NULL, 'Doxycycline.png'),
(14, 'Azithromycin', 'Zithromax', 'Macrolide antibiotic', 95, 7.00, '2026-09-01', '2025-06-10 18:04:38', 2, NULL, 'Azithromycin.png'),
(15, 'Cephalexin', 'Keflex', 'Cephalosporin antibiotic', 100, 5.90, '2026-12-01', '2025-06-10 18:04:38', 2, NULL, 'Cephalexin.png'),
(16, 'Clindamycin', 'Cleocin', 'Lincosamide antibiotic', 90, 6.80, '2026-06-01', '2025-06-10 18:04:38', 2, NULL, 'Clindamycin.png'),
(17, 'Erythromycin', 'Ery-Tab', 'Macrolide antibiotic', 80, 5.50, '2026-08-20', '2025-06-10 18:04:38', 2, NULL, 'Erythromycin.png'),
(18, 'Metronidazole', 'Flagyl', 'Antibiotic and antiprotozoal', 75, 4.75, '2026-07-15', '2025-06-10 18:04:38', 2, NULL, 'Metronidazole.png'),
(19, 'Fluoxetine', 'Prozac', 'SSRI used to treat depression', 100, 8.50, '2027-03-01', '2025-06-10 18:04:38', 3, NULL, 'Fluoxetine.png'),
(20, 'Sertraline', 'Zoloft', 'SSRI for depression and anxiety', 95, 8.25, '2026-12-15', '2025-06-10 18:04:38', 3, NULL, 'Sertraline.png'),
(21, 'Citalopram', 'Celexa', 'SSRI for mood disorders', 90, 7.90, '2027-01-30', '2025-06-10 18:04:38', 3, NULL, 'Citalopram.png'),
(22, 'Paroxetine', 'Paxil', 'SSRI for depression and OCD', 85, 7.60, '2026-11-30', '2025-06-10 18:04:38', 3, NULL, 'Paroxetine.png'),
(23, 'Venlafaxine', 'Effexor', 'SNRI for depression', 80, 9.00, '2026-10-10', '2025-06-10 18:04:38', 3, NULL, 'Venlafaxine.png'),
(24, 'Duloxetine', 'Cymbalta', 'SNRI for depression and anxiety', 75, 9.50, '2026-09-01', '2025-06-10 18:04:38', 3, NULL, 'Duloxetine.png'),
(25, 'Bupropion', 'Wellbutrin', 'Atypical antidepressant', 70, 10.00, '2026-08-20', '2025-06-10 18:04:38', 3, NULL, 'Bupropion.png'),
(26, 'Amitriptyline', 'Elavil', 'Tricyclic antidepressant', 65, 6.70, '2026-07-01', '2025-06-10 18:04:38', 3, NULL, 'Amitriptyline.png'),
(27, 'Escitalopram', 'Lexapro', 'SSRI for depression and anxiety', 90, 8.30, '2027-01-01', '2025-06-10 18:04:38', 3, NULL, 'Escitalopram.png'),
(28, 'Insulin', 'Humulin', 'Hormone for blood sugar control', 200, 25.00, '2026-11-01', '2025-06-10 18:04:38', 4, NULL, 'Insulin.png'),
(29, 'Metformin', 'Glucophage', 'First-line treatment for type 2 diabetes', 180, 7.25, '2026-10-01', '2025-06-10 18:04:38', 4, NULL, 'Metformin.png'),
(30, 'Glipizide', 'Glucotrol', 'Sulfonylurea to lower blood glucose', 150, 6.00, '2026-09-01', '2025-06-10 18:04:38', 4, NULL, 'Glipizide.png'),
(31, 'Glyburide', 'Diabeta', 'Oral diabetes medication', 140, 6.10, '2026-08-15', '2025-06-10 18:04:38', 4, NULL, 'Glyburide.png'),
(32, 'Pioglitazone', 'Actos', 'Increases insulin sensitivity', 130, 7.80, '2026-07-30', '2025-06-10 18:04:38', 4, NULL, 'Pioglitazone.png'),
(33, 'Sitagliptin', 'Januvia', 'DPP-4 inhibitor for diabetes', 125, 9.20, '2026-12-15', '2025-06-10 18:04:38', 4, NULL, 'Sitagliptin.png'),
(34, 'Empagliflozin', 'Jardiance', 'SGLT2 inhibitor for blood sugar control', 120, 10.50, '2026-11-30', '2025-06-10 18:04:38', 4, NULL, 'Empagliflozin.png'),
(35, 'Liraglutide', 'Victoza', 'GLP-1 receptor agonist', 110, 15.00, '2026-10-01', '2025-06-10 18:04:38', 4, NULL, 'Liraglutide.png'),
(36, 'Acarbose', 'Precose', 'Slows carbohydrate digestion', 100, 5.95, '2026-09-15', '2025-06-10 18:04:38', 4, NULL, 'Acarbose.png'),
(37, 'Phenytoin', 'Dilantin', 'Controls seizures', 90, 6.80, '2026-11-01', '2025-06-10 18:04:38', 5, NULL, 'Phenytoin.png'),
(38, 'Lamotrigine', 'Lamictal', 'Used for epilepsy and bipolar disorder', 85, 7.90, '2026-10-01', '2025-06-10 18:04:38', 5, NULL, 'Lamotrigine.png'),
(39, 'Valproic acid', 'Depakote', 'Seizure and mood stabilizer', 80, 8.50, '2026-09-01', '2025-06-10 18:04:38', 5, NULL, 'Valproic_acid.png'),
(40, 'Carbamazepine', 'Tegretol', 'Anticonvulsant and mood stabilizer', 75, 6.60, '2026-08-01', '2025-06-10 18:04:38', 5, NULL, 'Carbamazepine.png'),
(41, 'Levetiracetam', 'Keppra', 'Anticonvulsant', 70, 9.00, '2026-07-15', '2025-06-10 18:04:38', 5, NULL, 'Levetiracetam.png'),
(42, 'Topiramate', 'Topamax', 'Used for epilepsy and migraines', 65, 8.30, '2026-06-01', '2025-06-10 18:04:38', 5, NULL, 'Topiramate.png'),
(43, 'Gabapentin', 'Neurontin', 'Treats nerve pain and seizures', 60, 7.25, '2026-05-01', '2025-06-10 18:04:38', 5, NULL, 'Gabapentin.png'),
(44, 'Clonazepam', 'Klonopin', 'Benzodiazepine used for seizures', 55, 8.00, '2026-04-15', '2025-06-10 18:04:38', 5, NULL, 'Clonazepam.png'),
(45, 'Phenobarbital', 'Luminal', 'Long-acting barbiturate', 50, 7.10, '2026-03-01', '2025-06-10 18:04:38', 5, NULL, 'Phenobarbital.png'),
(46, 'Haloperidol', 'Haldol', 'Typical antipsychotic', 80, 6.90, '2026-12-01', '2025-06-10 18:04:38', 6, NULL, 'Haloperidol.png'),
(47, 'Risperidone', 'Risperdal', 'Atypical antipsychotic', 75, 7.80, '2026-11-01', '2025-06-10 18:04:38', 6, NULL, 'Risperidone.png'),
(48, 'Olanzapine', 'Zyprexa', 'Atypical antipsychotic', 70, 8.40, '2026-10-01', '2025-06-10 18:04:38', 6, NULL, 'Olanzapine.png'),
(49, 'Quetiapine', 'Seroquel', 'Treats schizophrenia and bipolar', 65, 9.20, '2026-09-01', '2025-06-10 18:04:38', 6, NULL, 'Quetiapine.png'),
(50, 'Aripiprazole', 'Abilify', 'Atypical antipsychotic', 60, 10.50, '2026-08-01', '2025-06-10 18:04:38', 6, NULL, 'Aripiprazole.png'),
(51, 'Clozapine', 'Clozaril', 'Used in treatment-resistant schizophrenia', 55, 12.00, '2026-07-01', '2025-06-10 18:04:38', 6, NULL, 'Clozapine.png'),
(52, 'Chlorpromazine', 'Thorazine', 'First-generation antipsychotic', 50, 6.50, '2026-06-01', '2025-06-10 18:04:38', 6, NULL, 'Chlorpromazine.png'),
(53, 'Ziprasidone', 'Geodon', 'Second-generation antipsychotic', 45, 9.10, '2026-05-01', '2025-06-10 18:04:38', 6, NULL, 'Ziprasidone.png'),
(54, 'Lurasidone', 'Latuda', 'Atypical antipsychotic', 40, 11.00, '2026-04-01', '2025-06-10 18:04:38', 6, NULL, 'Lurasidone.png'),
(55, 'Acyclovir', 'Zovirax', 'Antiviral for herpes viruses', 100, 6.50, '2026-12-01', '2025-06-10 18:04:38', 7, NULL, 'Acyclovir.png'),
(56, 'Oseltamivir (Tamiflu)', 'Tamiflu', 'Treats influenza', 90, 8.75, '2026-11-01', '2025-06-10 18:04:38', 7, NULL, 'Oseltamivir.png'),
(57, 'Valacyclovir', 'Valtrex', 'Antiviral for herpes and shingles', 80, 7.90, '2026-10-01', '2025-06-10 18:04:38', 7, NULL, 'Valacyclovir.png'),
(58, 'Remdesivir', 'Veklury', 'Antiviral for COVID-19', 70, 25.00, '2026-09-01', '2025-06-10 18:04:38', 7, NULL, 'Remdesivir.png'),
(59, 'Zidovudine (AZT)', 'Retrovir', 'HIV antiretroviral', 60, 10.25, '2026-08-01', '2025-06-10 18:04:38', 7, NULL, 'Zidovudine.png'),
(60, 'Sofosbuvir', 'Sovaldi', 'Used to treat hepatitis C', 50, 30.00, '2026-07-01', '2025-06-10 18:04:38', 7, NULL, 'Sofosbuvir.png'),
(61, 'Lamivudine', 'Epivir', 'HIV and hepatitis B treatment', 45, 9.40, '2026-06-01', '2025-06-10 18:04:38', 7, NULL, 'Lamivudine.png'),
(62, 'Abacavir', 'Ziagen', 'HIV medication', 40, 11.75, '2026-05-01', '2025-06-10 18:04:38', 7, NULL, 'Abacavir.png'),
(63, 'Tenofovir', 'Viread', 'Antiretroviral for HIV', 35, 12.50, '2026-04-01', '2025-06-10 18:04:38', 7, NULL, 'Tenofovir.png'),
(64, 'Warfarin', 'Coumadin', 'Vitamin K antagonist', 120, 4.20, '2026-12-01', '2025-06-10 18:04:38', 8, NULL, 'Warfarin.png'),
(65, 'Heparin', 'Heparin Sodium', 'Injectable anticoagulant', 110, 5.10, '2026-11-01', '2025-06-10 18:04:38', 8, NULL, 'Heparin.png'),
(66, 'Enoxaparin', 'Lovenox', 'Low molecular weight heparin', 100, 8.25, '2026-10-01', '2025-06-10 18:04:38', 8, NULL, 'Enoxaparin.png'),
(67, 'Dabigatran', 'Pradaxa', 'Direct thrombin inhibitor', 90, 9.50, '2026-09-01', '2025-06-10 18:04:38', 8, NULL, 'Dabigatran.png'),
(68, 'Apixaban', 'Eliquis', 'Factor Xa inhibitor', 80, 10.00, '2026-08-01', '2025-06-10 18:04:38', 8, NULL, 'Apixaban.png'),
(69, 'Rivaroxaban', 'Xarelto', 'Oral anticoagulant', 70, 10.50, '2026-07-01', '2025-06-10 18:04:38', 8, NULL, 'Rivaroxaban.png'),
(70, 'Fondaparinux', 'Arixtra', 'Synthetic anticoagulant', 60, 11.00, '2026-06-01', '2025-06-10 18:04:38', 8, NULL, 'Fondaparinux.png'),
(71, 'Edoxaban', 'Savaysa', 'Oral anticoagulant', 50, 9.90, '2026-05-01', '2025-06-10 18:04:38', 8, NULL, 'Edoxaban.png'),
(72, 'Aspirin', 'Ecotrin', 'Low-dose antiplatelet', 200, 2.50, '2026-04-01', '2025-06-10 18:04:38', 8, NULL, 'Aspirin.png'),
(73, 'Diphenhydramine', 'Benadryl', 'First-generation antihistamine', 150, 4.00, '2026-12-01', '2025-06-10 18:04:38', 9, NULL, 'Diphenhydramine.png'),
(74, 'Loratadine', 'Claritin', 'Second-generation antihistamine', 140, 5.00, '2026-11-01', '2025-06-10 18:04:38', 9, NULL, 'Loratadine.png'),
(75, 'Cetirizine', 'Zyrtec', 'Non-drowsy antihistamine', 130, 5.50, '2026-10-01', '2025-06-10 18:04:38', 9, NULL, 'Cetirizine.png'),
(76, 'Fexofenadine', 'Allegra', 'Second-generation antihistamine', 120, 6.00, '2026-09-01', '2025-06-10 18:04:38', 9, NULL, 'Fexofenadine.png'),
(77, 'Chlorpheniramine', 'Chlor-Trimeton', 'First-gen antihistamine', 110, 3.75, '2026-08-01', '2025-06-10 18:04:38', 9, NULL, 'Chlorpheniramine.png'),
(78, 'Hydroxyzine', 'Vistaril', 'Anxiety and allergy treatment', 100, 6.50, '2026-07-01', '2025-06-10 18:04:38', 9, NULL, 'Hydroxyzine.png'),
(79, 'Levocetirizine', 'Xyzal', 'Non-sedating antihistamine', 90, 5.90, '2026-06-01', '2025-06-10 18:04:38', 9, NULL, 'Levocetirizine.png'),
(80, 'Desloratadine', 'Clarinex', 'Long-acting antihistamine', 80, 6.25, '2026-05-01', '2025-06-10 18:04:38', 9, NULL, 'Desloratadine.png'),
(81, 'Promethazine', 'Phenergan', 'Antihistamine for nausea/allergy', 70, 4.95, '2026-04-01', '2025-06-10 18:04:38', 9, NULL, 'Promethazine.png');

-- --------------------------------------------------------

--
-- Table structure for table `sales`
--

CREATE TABLE `sales` (
  `sale_id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `medicine_id` int(11) DEFAULT NULL,
  `quantity` int(11) DEFAULT NULL,
  `total_price` decimal(10,2) DEFAULT NULL,
  `sale_date` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `sales`
--

INSERT INTO `sales` (`sale_id`, `user_id`, `medicine_id`, `quantity`, `total_price`, `sale_date`) VALUES
(17, 2, 11, 10, 55.00, '2025-06-24 11:43:28'),
(18, 2, 12, 10, 62.00, '2025-06-24 11:43:28'),
(19, 2, 8, 1, 8.75, '2025-06-25 15:12:45'),
(20, 2, 9, 1, 9.20, '2025-06-25 15:12:45'),
(21, 2, 6, 1, 12.99, '2025-06-25 15:12:45');

-- --------------------------------------------------------

--
-- Table structure for table `suppliers`
--

CREATE TABLE `suppliers` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `contact` varchar(20) DEFAULT NULL,
  `email` varchar(100) DEFAULT NULL,
  `address` text DEFAULT NULL,
  `category_id` int(11) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `suppliers`
--

INSERT INTO `suppliers` (`id`, `name`, `contact`, `email`, `address`, `category_id`, `created_at`) VALUES
(1, 'HealthPro Distributors', '09171234567', 'contact@healthpro.com', '123 Medical St., QC', 1, '2025-06-10 18:15:54'),
(2, 'PharmaLink Inc.', '09998887766', 'info@pharmalink.ph', '456 Wellness Ave., Makati', 4, '2025-06-10 18:15:54'),
(3, 'MediSupply Co.', '09223334455', 'support@medisupply.com', '789 Botika Blvd., Manila', 1, '2025-06-10 18:15:54');

-- --------------------------------------------------------

--
-- Table structure for table `tbl_user`
--

CREATE TABLE `tbl_user` (
  `userID` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  `address` text DEFAULT NULL,
  `contact` varchar(11) DEFAULT NULL,
  `role` varchar(20) NOT NULL DEFAULT 'user'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `tbl_user`
--

INSERT INTO `tbl_user` (`userID`, `username`, `password`, `address`, `contact`, `role`) VALUES
(1, 'admin', 'admin', 'PUP STC', '09123456789', 'admin'),
(2, 'piyo', 'hoho', 'Altura Bata', '09123456789', 'user'),
(5, 'Pi', 'hoho', 'Altura Bata', '09123456789', 'user'),
(9, 'Jade', 'hehe', 'Altura Bata', '09166314087', 'user'),
(12, 'Jerome', 'Jerome', '', '', 'admin'),
(13, 'Jamie', 'Jamie', '', '', 'admin'),
(14, 'Fiona', 'Fiona', '', '', 'admin'),
(15, 'Marcus', 'Marcus', '', '', 'admin'),
(16, 'username', 'password', '', '', 'user');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `categories`
--
ALTER TABLE `categories`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `medicines`
--
ALTER TABLE `medicines`
  ADD PRIMARY KEY (`medicine_id`),
  ADD KEY `category_id` (`category_id`);

--
-- Indexes for table `sales`
--
ALTER TABLE `sales`
  ADD PRIMARY KEY (`sale_id`),
  ADD KEY `fk_sales_user` (`user_id`),
  ADD KEY `fk_sales_medicine` (`medicine_id`);

--
-- Indexes for table `suppliers`
--
ALTER TABLE `suppliers`
  ADD PRIMARY KEY (`id`),
  ADD KEY `category_id` (`category_id`);

--
-- Indexes for table `tbl_user`
--
ALTER TABLE `tbl_user`
  ADD PRIMARY KEY (`userID`),
  ADD UNIQUE KEY `username` (`username`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `categories`
--
ALTER TABLE `categories`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT for table `medicines`
--
ALTER TABLE `medicines`
  MODIFY `medicine_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=82;

--
-- AUTO_INCREMENT for table `sales`
--
ALTER TABLE `sales`
  MODIFY `sale_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=22;

--
-- AUTO_INCREMENT for table `suppliers`
--
ALTER TABLE `suppliers`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `tbl_user`
--
ALTER TABLE `tbl_user`
  MODIFY `userID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=17;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `medicines`
--
ALTER TABLE `medicines`
  ADD CONSTRAINT `medicines_ibfk_1` FOREIGN KEY (`category_id`) REFERENCES `categories` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
